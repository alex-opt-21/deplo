const DEFAULT_API_URL = "https://portafy.onrender.com/api";

function normalizeUrl(url, fallback) {
  const value = String(url || fallback || "").trim();
  return value.replace(/\/+$/, "");
}

const apiUrl = normalizeUrl(
  window.__APP_CONFIG__?.apiUrl ?? import.meta.env.VITE_API_URL,
  DEFAULT_API_URL
);

const backendUrl = normalizeUrl(
  window.__APP_CONFIG__?.backendUrl ?? import.meta.env.VITE_BACKEND_URL,
  apiUrl.replace(/\/api$/, "")
);

const config = {
  apiUrl,
  backendUrl,
  googleClientId: window.__APP_CONFIG__?.googleClientId ?? import.meta.env.VITE_GOOGLE_CLIENT_ID ?? "",
  linkedinClientId: window.__APP_CONFIG__?.linkedinClientId ?? import.meta.env.VITE_LINKEDIN_CLIENT_ID ?? "",
  linkedinRedirectUri:
    window.__APP_CONFIG__?.linkedinRedirectUri ?? import.meta.env.VITE_LINKEDIN_REDIRECT_URI ?? "",
  githubClientId: window.__APP_CONFIG__?.githubClientId ?? import.meta.env.VITE_GITHUB_CLIENT_ID ?? "",
  githubRedirectUri:
    window.__APP_CONFIG__?.githubRedirectUri ?? import.meta.env.VITE_GITHUB_REDIRECT_URI ?? "",
  recaptchaSiteKey:
    window.__APP_CONFIG__?.recaptchaSiteKey ?? import.meta.env.VITE_RECAPTCHA_SITE_KEY ?? "",
  authStorageKeys: {
    user: "user",
    token: "token",
    legacyToken: "AUTH_TOKEN",
    oauthResult: "oauth_result",
  },
};

export default config;
