export function isIosSafari(): boolean {
  // Safety check for SSR environments (Next.js, etc.)
  if (typeof navigator === "undefined") {
    return false;
  }

  const ua = navigator.userAgent;

  // Step 1: Detect iOS / iPadOS device
  // - Classic devices: iPhone, iPad, iPod
  // - Modern iPads (iPadOS ≥ 13): platform === "MacIntel" + multitouch support
  const isIOSDevice =
    /iPhone|iPad|iPod/.test(ua) ||
    (navigator.platform === "MacIntel" && navigator.maxTouchPoints && navigator.maxTouchPoints > 1);

  if (!isIOSDevice) {
    return false;
  }

  // Step 2: Check that it's actually Safari
  // Native Safari contains "Safari/" (or just "Version/" in some old WebKit builds)
  const hasSafariToken = /Safari\//.test(ua) || /Version\//.test(ua);

  // Step 3: Exclude known non-Safari browsers on iOS
  // These browsers add their own signature while still using WebKit
  const isNonSafariBrowser =
    /CriOS\//.test(ua) ||   // Chrome
    /FxiOS\//.test(ua) ||   // Firefox
    /EdgiOS\//.test(ua) ||  // Edge
    /OPiOS\//.test(ua) ||   // Opera
    /OPT\//.test(ua) ||     // Opera Touch / GX
    /Brave\//.test(ua) ||   // Brave
    /DuckDuckGo\//.test(ua); // DuckDuckGo Privacy Browser

  return hasSafariToken && !isNonSafariBrowser;
}