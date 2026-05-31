import { useState } from "react";

const ACCENT = "#e8701a";

/* ─── Avatar ─────────────────────────────────────────────── */
function Avatar({ name, size = "lg" }: { name: string; size?: "sm" | "lg" }) {
  const initials = name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
  const dim = size === "lg" ? "h-20 w-20 text-2xl" : "h-7 w-7 text-xs";
  return (
    <div
      className={`relative flex shrink-0 items-center justify-center rounded-full font-bold text-white select-none ${dim}`}
      style={{ background: "linear-gradient(135deg,#e8701a 0%,#f5a623 100%)" }}
    >
      {initials}
      {size === "lg" && (
        <span className="absolute bottom-0.5 right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-400" />
      )}
    </div>
  );
}

/* ─── Navbar ─────────────────────────────────────────────── */
function NavBar({ userName }: { userName: string }) {
  return (
    <nav className="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-gray-100 bg-white px-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
      <img src="/images/printsync-logo.png" alt="PrintSync" className="h-8 w-auto object-contain" />

      <div className="hidden sm:flex items-center gap-7 text-[13px] text-gray-500 font-medium">
        {[
          {
            label: "Dashboard",
            icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />,
          },
          {
            label: "Store",
            icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />,
          },
          {
            label: "View Orders",
            icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />,
          },
        ].map(({ label, icon }) => (
          <a key={label} href="#" className="flex items-center gap-1.5 hover:text-gray-800 transition-colors">
            <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{icon}</svg>
            {label}
          </a>
        ))}
      </div>

      <button className="flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-[13px] font-medium text-gray-700 hover:bg-gray-100 transition-colors">
        <Avatar name={userName} size="sm" />
        {userName}
        <svg className="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </nav>
  );
}

/* ─── Input Field ────────────────────────────────────────── */
interface FieldProps {
  label: string;
  id: string;
  type?: string;
  value: string;
  onChange: (v: string) => void;
  placeholder?: string;
  icon?: React.ReactNode;
  readOnly?: boolean;
}

function Field({ label, id, type = "text", value, onChange, placeholder, icon, readOnly }: FieldProps) {
  const [focused, setFocused] = useState(false);
  return (
    <div className="flex flex-col gap-1.5">
      <label htmlFor={id} className="text-xs font-semibold uppercase tracking-wide text-gray-500">
        {label}
      </label>
      <div
        className="flex items-center gap-2.5 rounded-lg border bg-white px-3.5 py-2.5 transition-all duration-150"
        style={{
          borderColor: focused ? ACCENT : "#e5e7eb",
          boxShadow: focused ? `0 0 0 3px ${ACCENT}1a` : "0 1px 2px rgba(0,0,0,0.04)",
        }}
      >
        {icon && <span className="shrink-0 text-gray-400">{icon}</span>}
        <input
          id={id}
          type={type}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          placeholder={placeholder}
          readOnly={readOnly}
          onFocus={() => setFocused(true)}
          onBlur={() => setFocused(false)}
          className={`w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none ${
            readOnly ? "cursor-default text-gray-400" : ""
          }`}
        />
        {readOnly && (
          <span className="shrink-0 rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-400 tracking-wide">
            LOCKED
          </span>
        )}
      </div>
    </div>
  );
}

/* ─── Toggle ─────────────────────────────────────────────── */
function Toggle({ defaultOn, accent }: { defaultOn: boolean; accent: string }) {
  const [on, setOn] = useState(defaultOn);
  return (
    <button
      role="switch"
      aria-checked={on}
      onClick={() => setOn((v) => !v)}
      className="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2"
      style={{ backgroundColor: on ? accent : "#d1d5db" }}
    >
      <span
        className="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow-md transition-transform duration-200 ease-in-out"
        style={{ transform: on ? "translateX(24px)" : "translateX(4px)" }}
      />
    </button>
  );
}

/* ─── Toast ──────────────────────────────────────────────── */
function Toast({ message, onClose }: { message: string; onClose: () => void }) {
  return (
    <div className="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl bg-gray-900 px-5 py-3.5 text-sm text-white shadow-2xl animate-slide-up">
      <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400">
        <svg className="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
        </svg>
      </span>
      <span className="font-medium">{message}</span>
      <button onClick={onClose} className="ml-1 text-gray-500 hover:text-white transition-colors">
        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  );
}

/* ─── Section Card wrapper ───────────────────────────────── */
function Card({ children }: { children: React.ReactNode }) {
  return (
    <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
      {children}
    </div>
  );
}

/* ─── App ────────────────────────────────────────────────── */
export default function App() {
  const [firstName, setFirstName] = useState("Kyla");
  const [lastName, setLastName] = useState("Abejsuro");
  const [phone, setPhone] = useState("");
  const [email] = useState("kylanidea@gmail.com");
  const [address, setAddress] = useState("");
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState(false);
  const [activeTab, setActiveTab] = useState<"profile" | "security" | "notifications">("profile");

  const fullName = `${firstName} ${lastName}`.trim();

  const handleSave = async () => {
    setSaving(true);
    await new Promise((r) => setTimeout(r, 1000));
    setSaving(false);
    setToast(true);
    setTimeout(() => setToast(false), 3500);
  };

  const tabs: { id: "profile" | "security" | "notifications"; label: string; icon: React.ReactNode }[] = [
    {
      id: "profile",
      label: "Profile",
      icon: (
        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      ),
    },
    {
      id: "security",
      label: "Security",
      icon: (
        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      ),
    },
    {
      id: "notifications",
      label: "Notifications",
      icon: (
        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
      ),
    },
  ];

  return (
    <div className="min-h-screen bg-[#f7f8fa]">
      <NavBar userName={firstName} />

      <main className="mx-auto max-w-4xl px-4 py-10">
        {/* Page header */}
        <div className="mb-8">
          <h1 className="text-xl font-bold text-gray-900">Account Settings</h1>
          <p className="mt-0.5 text-sm text-gray-500">Manage your profile, security, and notification preferences.</p>
        </div>

        <div className="flex flex-col gap-5 lg:flex-row lg:items-start">

          {/* ── Sidebar ── */}
          <aside className="w-full lg:w-56 shrink-0 flex flex-col gap-4">

            {/* 1️⃣ User info card — on top */}
            <Card>
              <div className="flex flex-col items-center gap-2 px-4 py-6">
                <Avatar name={fullName || "KA"} size="lg" />
                <div className="text-center mt-1">
                  <p className="text-sm font-semibold text-gray-800">{fullName}</p>
                  <p className="text-[11px] text-gray-400 mt-0.5 break-all">{email}</p>
                </div>
                <span
                  className="mt-1 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-semibold"
                  style={{ background: `${ACCENT}18`, color: ACCENT }}
                >
                  <span className="h-1.5 w-1.5 rounded-full bg-emerald-400 inline-block" />
                  Active Account
                </span>
              </div>
            </Card>

            {/* 2️⃣ Nav tabs — below */}
            <Card>
              <nav className="p-1.5 flex flex-col gap-0.5">
                {tabs.map((tab) => {
                  const active = activeTab === tab.id;
                  return (
                    <button
                      key={tab.id}
                      onClick={() => setActiveTab(tab.id)}
                      className="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-150"
                      style={active ? { background: `${ACCENT}14`, color: ACCENT } : { color: "#6b7280" }}
                    >
                      <span style={active ? { color: ACCENT } : { color: "#9ca3af" }}>{tab.icon}</span>
                      {tab.label}
                      {active && (
                        <span className="ml-auto h-1.5 w-1.5 rounded-full" style={{ background: ACCENT }} />
                      )}
                    </button>
                  );
                })}
              </nav>
            </Card>
          </aside>

          {/* ── Content ── */}
          <div className="flex-1 min-w-0">

            {/* ── Profile Tab ── */}
            {activeTab === "profile" && (
              <Card>
                {/* Header */}
                <div className="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                  <div>
                    <h2 className="text-sm font-semibold text-gray-900">Personal Information</h2>
                    <p className="text-xs text-gray-400 mt-0.5">Update your name, contact and address details.</p>
                  </div>
                  <span
                    className="hidden sm:flex h-9 w-9 items-center justify-center rounded-full"
                    style={{ background: `${ACCENT}12` }}
                  >
                    <svg className="h-4 w-4" style={{ color: ACCENT }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </span>
                </div>

                {/* Avatar upload */}
                <div className="flex items-center gap-4 border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                  <Avatar name={fullName || "KA"} size="lg" />
                  <div>
                    <p className="text-sm font-semibold text-gray-800">Profile Photo</p>
                    <p className="text-xs text-gray-400 mt-0.5">JPG, PNG or GIF · Max 2 MB</p>
                    <div className="mt-2.5 flex gap-2">
                      <button
                        className="rounded-lg px-3.5 py-1.5 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                        style={{ background: ACCENT }}
                      >
                        Upload photo
                      </button>
                      <button className="rounded-lg border border-gray-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-500 hover:bg-gray-100 transition-colors">
                        Remove
                      </button>
                    </div>
                  </div>
                </div>

                {/* Form */}
                <div className="px-6 py-5 space-y-4">
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Field
                      label="First Name"
                      id="firstName"
                      value={firstName}
                      onChange={setFirstName}
                      placeholder="First name"
                      icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>}
                    />
                    <Field
                      label="Last Name"
                      id="lastName"
                      value={lastName}
                      onChange={setLastName}
                      placeholder="Last name"
                      icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>}
                    />
                  </div>
                  <Field
                    label="Phone Number"
                    id="phone"
                    type="tel"
                    value={phone}
                    onChange={setPhone}
                    placeholder="+1 (555) 000-0000"
                    icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>}
                  />
                  <Field
                    label="Email Address"
                    id="email"
                    type="email"
                    value={email}
                    onChange={() => {}}
                    readOnly
                    icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>}
                  />
                  <Field
                    label="Address"
                    id="address"
                    value={address}
                    onChange={setAddress}
                    placeholder="123 Main St, City, Country"
                    icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>}
                  />
                </div>

                {/* Footer */}
                <div className="flex items-center justify-between border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                  <p className="text-xs text-gray-400">Last updated · just now</p>
                  <div className="flex gap-2.5">
                    <button className="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                      Cancel
                    </button>
                    <button
                      onClick={handleSave}
                      disabled={saving}
                      className="flex items-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-all active:scale-95 disabled:opacity-60"
                      style={{ background: ACCENT }}
                    >
                      {saving ? (
                        <>
                          <svg className="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                          </svg>
                          Saving…
                        </>
                      ) : (
                        <>
                          <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                          </svg>
                          Save Changes
                        </>
                      )}
                    </button>
                  </div>
                </div>
              </Card>
            )}

            {/* ── Security Tab ── */}
            {activeTab === "security" && (
              <Card>
                <div className="border-b border-gray-100 px-6 py-4">
                  <h2 className="text-sm font-semibold text-gray-900">Security Settings</h2>
                  <p className="text-xs text-gray-400 mt-0.5">Manage your password and account security.</p>
                </div>
                <div className="px-6 py-5 space-y-4">
                  {[
                    { label: "Current Password", id: "cur" },
                    { label: "New Password", id: "new" },
                    { label: "Confirm New Password", id: "conf" },
                  ].map((f) => (
                    <Field
                      key={f.id}
                      label={f.label}
                      id={f.id}
                      type="password"
                      value=""
                      onChange={() => {}}
                      placeholder="••••••••"
                      icon={<svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>}
                    />
                  ))}

                  <div
                    className="flex items-start gap-3.5 rounded-xl border p-4 mt-2"
                    style={{ borderColor: `${ACCENT}30`, background: `${ACCENT}08` }}
                  >
                    <svg className="h-5 w-5 mt-0.5 shrink-0" style={{ color: ACCENT }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <div>
                      <p className="text-sm font-semibold text-gray-800">Two-Factor Authentication</p>
                      <p className="text-xs text-gray-500 mt-0.5">Add an extra layer of security to your account.</p>
                      <button className="mt-2 text-xs font-semibold" style={{ color: ACCENT }}>Enable 2FA →</button>
                    </div>
                  </div>
                </div>
                <div className="flex justify-end border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                  <button
                    className="rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style={{ background: ACCENT }}
                  >
                    Update Password
                  </button>
                </div>
              </Card>
            )}

            {/* ── Notifications Tab ── */}
            {activeTab === "notifications" && (
              <Card>
                <div className="border-b border-gray-100 px-6 py-4">
                  <h2 className="text-sm font-semibold text-gray-900">Notification Preferences</h2>
                  <p className="text-xs text-gray-400 mt-0.5">Choose how and when you want to be notified.</p>
                </div>
                <div className="divide-y divide-gray-100">
                  {[
                    { label: "Order updates", desc: "Get notified when your order status changes.", on: true },
                    { label: "Promotions & offers", desc: "Receive emails about deals and discounts.", on: true },
                    { label: "Security alerts", desc: "Important alerts about your account security.", on: false },
                    { label: "Newsletter", desc: "Weekly digest of news and product updates.", on: false },
                  ].map((item, i) => (
                    <div key={i} className="flex items-center justify-between gap-4 px-6 py-4">
                      <div>
                        <p className="text-sm font-semibold text-gray-800">{item.label}</p>
                        <p className="text-xs text-gray-400 mt-0.5">{item.desc}</p>
                      </div>
                      <Toggle defaultOn={item.on} accent={ACCENT} />
                    </div>
                  ))}
                </div>
                <div className="flex justify-end border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                  <button
                    className="rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style={{ background: ACCENT }}
                  >
                    Save Preferences
                  </button>
                </div>
              </Card>
            )}
          </div>
        </div>
      </main>

      {toast && <Toast message="Profile updated successfully!" onClose={() => setToast(false)} />}
    </div>
  );
}
