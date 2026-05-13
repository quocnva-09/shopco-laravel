<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Security headers are HTTP response headers that instruct the browser
     * how to behave when rendering or interacting with the application.
     * They are completely independent of port numbers, SSL termination,
     * or Cloudflare — they are simply name/value pairs attached to the
     * HTTP response before it leaves the application.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ──────────────────────────────────────────────────────────────
        // X-Frame-Options: DENY
        //
        // Prevents this application from being embedded inside an <iframe>
        // on any other website. Without this, an attacker can overlay an
        // invisible iframe over a legitimate page to trick users into
        // clicking buttons they can't see ("clickjacking").
        //
        // DENY  → no framing allowed at all (safest for an API).
        // ──────────────────────────────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'DENY');

        // ──────────────────────────────────────────────────────────────
        // X-Content-Type-Options: nosniff
        //
        // Forces the browser to honour the Content-Type the server declares
        // instead of guessing ("sniffing") the MIME type itself. Without this,
        // a browser might execute a file served as text/plain if the bytes
        // look like JavaScript — a classic XSS vector.
        // ──────────────────────────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ──────────────────────────────────────────────────────────────
        // Referrer-Policy: strict-origin-when-cross-origin
        //
        // Controls what information is sent in the HTTP Referer header.
        //
        // strict-origin-when-cross-origin:
        //   - Same-origin request  → full URL is sent.
        //   - Cross-origin request → only the origin (scheme + host) is sent.
        //   - HTTP → HTTPS         → Referer is omitted entirely.
        //
        // This prevents internal API paths (e.g. /api/admin/orders/42) from
        // leaking to third-party services loaded by the frontend.
        // ──────────────────────────────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ──────────────────────────────────────────────────────────────
        // Permissions-Policy
        //
        // Explicitly revokes browser APIs that this application does not use.
        // Even if a future XSS manages to execute JavaScript, it cannot
        // silently access the user's camera, microphone, or location.
        // ──────────────────────────────────────────────────────────────
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        // ──────────────────────────────────────────────────────────────
        // Content-Security-Policy (CSP)
        //
        // CSP is the most powerful header — it whitelists exactly which
        // sources of scripts, styles, images, etc. the browser is allowed
        // to load and execute. Anything not whitelisted is blocked.
        //
        // TWO-TIER STRATEGY because Swagger UI requires inline scripts:
        //
        //   • /api/documentation* paths → relaxed CSP so Swagger UI works.
        //     Allows inline scripts/styles and scripts from cdn.jsdelivr.net
        //     (where Swagger UI loads its assets).
        //
        //   • All other /api/* paths   → strict CSP (default-src 'self').
        //     API responses are JSON — browsers should never execute
        //     anything from an API response.
        // ──────────────────────────────────────────────────────────────
        if ($request->is('api/documentation*') || $request->is('docs*')) {
            // Relaxed — Swagger UI needs unsafe-inline and CDN assets.
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com",
                "font-src 'self' fonts.gstatic.com cdn.jsdelivr.net",
                "img-src 'self' data:",
                "connect-src 'self'",
            ]);
        } else {
            // Strict — API-only responses should never load external resources.
            $csp = "default-src 'self'";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
