// In-app browser detection + redirect helpers.
//
// Google blocks OAuth inside embedded WebViews (Facebook, Instagram, TikTok,
// Gmail, etc.) with `Error 403: disallowed_useragent` per its "Use secure
// browsers" policy. We detect those user agents and offer a one-tap redirect
// into the system browser so Google sign-in works.

const IN_APP_PATTERNS = [
    /FBAN|FBAV|FB_IAB|FB4A|FBIOS|FBSS/i, // Facebook + Messenger
    /Instagram/i,
    /TikTok|musical_ly|BytedanceWebview/i,
    /LinkedInApp/i,
    /Twitter/i, // includes X
    /Pinterest/i,
    /Snapchat/i,
    /Line\//i,
    /MicroMessenger/i, // WeChat
    /GSA\//i, // Google Search App / Gmail in-app on iOS
];

export function isInAppBrowser(ua) {
    const userAgent =
        ua ??
        (typeof navigator !== "undefined" ? navigator.userAgent : "");
    if (!userAgent) return false;
    return IN_APP_PATTERNS.some((re) => re.test(userAgent));
}

export function detectOS(ua) {
    const userAgent =
        ua ??
        (typeof navigator !== "undefined" ? navigator.userAgent : "");
    if (!userAgent) return "other";
    if (/iPhone|iPad|iPod/i.test(userAgent)) return "ios";
    if (/Android/i.test(userAgent)) return "android";
    return "other";
}

function buildAndroidIntent(absoluteUrl) {
    const noScheme = absoluteUrl.replace(/^https?:\/\//, "");
    // `package=com.android.chrome` opens Chrome directly. If Chrome is not
    // installed, browser_fallback_url falls back to the system default browser.
    return (
        `intent://${noScheme}` +
        `#Intent;scheme=https;package=com.android.chrome;` +
        `S.browser_fallback_url=${encodeURIComponent(absoluteUrl)};end`
    );
}

function toAbsoluteUrl(targetUrl) {
    try {
        return new URL(targetUrl, window.location.origin).toString();
    } catch (e) {
        return targetUrl;
    }
}

window.inAppBrowserGuard = function (targetUrl) {
    return {
        os: "other",
        isInApp: false,
        showModal: false,
        targetUrl,

        init() {
            if (typeof navigator === "undefined") return;
            this.os = detectOS();
            this.isInApp = isInAppBrowser();
        },

        handleClick(event) {
            if (!this.isInApp) return; // real browser — let the link follow normally
            event.preventDefault();
            this.showModal = true;
        },

        openExternal() {
            const absoluteUrl = toAbsoluteUrl(this.targetUrl);
            if (this.os === "android") {
                window.location.href = buildAndroidIntent(absoluteUrl);
                return;
            }
            if (this.os === "ios") {
                // Works in Facebook/Messenger/Instagram. TikTok, LinkedIn and
                // some others ignore it — hence the manual instructions stay
                // visible in the modal as a fallback.
                window.location.href = absoluteUrl.replace(
                    /^https:\/\//,
                    "x-safari-https://"
                );
                return;
            }
            window.location.href = absoluteUrl;
        },
    };
};

window.isInAppBrowser = isInAppBrowser;
window.detectOS = detectOS;
