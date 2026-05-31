import { useState, useMemo } from "react";

type ServiceTier = "standard" | "urgent" | "rush";
type InvoiceType = "digital" | "physical";

const TIER_CONFIG: Record<ServiceTier, { label: string; eta: string; surcharge: number; tag?: string }> = {
  standard: { label: "Standard", eta: "2-3 days", surcharge: 0, tag: "Base" },
  urgent: { label: "Urgent", eta: "24-48h", surcharge: 0.3 },
  rush: { label: "Rush", eta: "Same day", surcharge: 0.6 },
};

const BASE_PRICE = 500;

export default function App() {
  const [tier, setTier] = useState<ServiceTier>("standard");
  const [quantity, setQuantity] = useState<number>(1);
  const [date, setDate] = useState<string>("2026-05-28");
  const [notes, setNotes] = useState<string>("");
  const [wantInvoice, setWantInvoice] = useState<boolean>(false);
  const [invoiceType, setInvoiceType] = useState<InvoiceType>("digital");
  const [open, setOpen] = useState<boolean>(true);

  const totalPrice = useMemo(() => {
    const tierMult = 1 + TIER_CONFIG[tier].surcharge;
    return BASE_PRICE * quantity * tierMult;
  }, [tier, quantity]);

  const handleCheckout = () => {
    const payload = {
      tier,
      quantity,
      date,
      notes,
      invoice: wantInvoice ? invoiceType : null,
      total: totalPrice,
    };
    console.log("Order submitted:", payload);
    alert(
      `Order placed!\n\nTier: ${TIER_CONFIG[tier].label}\nQty: ${quantity}\nTotal: ₱${totalPrice.toFixed(2)}\n${
        wantInvoice ? `Invoice: ${invoiceType}` : "No invoice"
      }`
    );
  };

  if (!open) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-200">
        <button
          onClick={() => setOpen(true)}
          className="px-6 py-3 rounded-xl bg-slate-900 text-white font-medium hover:bg-slate-800 transition"
        >
          Open Order Form
        </button>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-300/80 p-4 sm:p-8">
      {/* Backdrop */}
      <div className="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" />

      {/* Modal */}
      <div className="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
        {/* Header with signage imagery */}
        <div className="relative h-32 sm:h-36 bg-slate-900 overflow-hidden">
          {/* Decorative signage wall background */}
          <div className="absolute inset-0">
            <div className="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/90 z-10" />
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(255,255,255,0.15),transparent_50%)]" />
            {/* Framed signs mockup */}
            <div className="absolute top-3 left-6 w-16 h-20 border-4 border-white/80 bg-amber-100/30 rotate-[-3deg]" />
            <div className="absolute top-6 left-28 w-20 h-16 border-4 border-white/80 bg-emerald-100/30 rotate-[2deg]" />
            <div className="absolute top-2 left-52 w-14 h-24 border-4 border-white/80 bg-rose-100/30 rotate-[-1deg]" />
            <div className="absolute top-8 left-72 w-24 h-14 border-4 border-white/80 bg-sky-100/30 rotate-[3deg]" />
            <div className="absolute top-4 left-[22rem] hidden sm:block w-20 h-20 border-4 border-white/80 bg-violet-100/30 rotate-[-2deg]" />
            {/* Shelf */}
            <div className="absolute bottom-8 left-0 right-0 h-1.5 bg-white/40" />
          </div>

          {/* Close button */}
          <button
            onClick={() => setOpen(false)}
            className="absolute top-4 right-4 z-20 h-9 w-9 rounded-full bg-white/95 hover:bg-white text-slate-700 flex items-center justify-center shadow-lg transition"
            aria-label="Close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2.2} className="h-4 w-4">
              <path d="M18 6L6 18M6 6l12 12" strokeLinecap="round" />
            </svg>
          </button>

          {/* Title */}
          <div className="absolute left-6 bottom-4 z-10 text-white">
            <h1 className="text-2xl sm:text-3xl font-bold tracking-tight">Signage</h1>
            <p className="text-sm sm:text-base text-white/80 mt-0.5">
              Professional signage printing for businesses and events.
            </p>
          </div>
        </div>

        {/* Scrollable body */}
        <div className="overflow-y-auto px-5 sm:px-8 py-6 space-y-6">
          {/* Service tiers */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {(Object.keys(TIER_CONFIG) as ServiceTier[]).map((key) => {
              const cfg = TIER_CONFIG[key];
              const active = tier === key;
              return (
                <button
                  key={key}
                  type="button"
                  onClick={() => setTier(key)}
                  className={[
                    "text-left rounded-xl border-2 p-4 transition-all",
                    active
                      ? "border-slate-900 bg-slate-900 text-white shadow-md"
                      : "border-slate-200 bg-white text-slate-900 hover:border-slate-300",
                  ].join(" ")}
                >
                  <div className="flex items-start justify-between">
                    <div>
                      <div className="font-semibold text-base">{cfg.label}</div>
                      <div className={`text-xs mt-0.5 ${active ? "text-white/70" : "text-slate-500"}`}>
                        {cfg.eta}
                      </div>
                    </div>
                    {active && (
                      <div className="h-5 w-5 rounded-full bg-white/10 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={3} className="h-3 w-3">
                          <path d="M20 6L9 17l-5-5" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </div>
                    )}
                  </div>
                  <div
                    className={`mt-3 inline-block text-xs font-semibold px-2 py-0.5 rounded ${
                      active ? "bg-white/15 text-white" : "bg-slate-100 text-slate-700"
                    }`}
                  >
                    {cfg.surcharge === 0 ? "Base" : `+${Math.round(cfg.surcharge * 100)}%`}
                  </div>
                </button>
              );
            })}
          </div>

          {/* Price + Quantity */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold text-slate-900 mb-2">Price</label>
              <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 font-medium">
                ₱{totalPrice.toFixed(2)}
              </div>
            </div>
            <div>
              <label className="block text-sm font-semibold text-slate-900 mb-2">Quantity</label>
              <div className="flex items-center rounded-xl border border-slate-200 bg-white">
                <button
                  type="button"
                  onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                  className="h-11 w-11 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"
                  aria-label="Decrease quantity"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2.2} className="h-4 w-4">
                    <path d="M5 12h14" strokeLinecap="round" />
                  </svg>
                </button>
                <div className="flex-1 text-center font-semibold text-slate-900">{quantity}</div>
                <button
                  type="button"
                  onClick={() => setQuantity((q) => q + 1)}
                  className="h-11 w-11 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"
                  aria-label="Increase quantity"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2.2} className="h-4 w-4">
                    <path d="M12 5v14M5 12h14" strokeLinecap="round" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          {/* Date */}
          <div>
            <label className="block text-sm font-semibold text-slate-900 mb-2">When do you need this?</label>
            <div className="relative">
              <input
                type="date"
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
              />
            </div>
          </div>

          {/* Notes */}
          <div>
            <label className="block text-sm font-semibold text-slate-900 mb-2">Additional notes</label>
            <textarea
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              rows={3}
              placeholder="Quantity, specifications, file references..."
              className="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 resize-none"
            />
          </div>

          {/* Invoice preference */}
          <div className="space-y-3">
            <label className="flex items-center gap-3 cursor-pointer select-none">
              <div className="relative">
                <input
                  type="checkbox"
                  checked={wantInvoice}
                  onChange={(e) => setWantInvoice(e.target.checked)}
                  className="peer sr-only"
                />
                <div className="h-6 w-6 rounded-md border-2 border-slate-300 bg-white peer-checked:border-slate-900 peer-checked:bg-slate-900 transition flex items-center justify-center">
                  {wantInvoice && (
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth={3} className="h-3.5 w-3.5">
                      <path d="M20 6L9 17l-5-5" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  )}
                </div>
              </div>
              <span className="text-slate-900 font-medium">I want an invoice for this order</span>
            </label>

            {/* Follow-up: digital or physical */}
            {wantInvoice && (
              <div className="ml-9 pl-4 border-l-2 border-slate-200 animate-[fadeIn_0.2s_ease-out]">
                <p className="text-sm font-semibold text-slate-800 mb-3">
                  How would you like to receive your invoice?
                </p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <label
                    className={[
                      "flex items-center gap-3 rounded-xl border-2 px-4 py-3 cursor-pointer transition",
                      invoiceType === "digital"
                        ? "border-slate-900 bg-slate-900 text-white"
                        : "border-slate-200 bg-white text-slate-900 hover:border-slate-300",
                    ].join(" ")}
                  >
                    <input
                      type="radio"
                      name="invoiceType"
                      value="digital"
                      checked={invoiceType === "digital"}
                      onChange={(e) => setInvoiceType(e.target.value as InvoiceType)}
                      className="sr-only"
                    />
                    <div
                      className={[
                        "h-5 w-5 rounded-full border-2 flex items-center justify-center",
                        invoiceType === "digital" ? "border-white" : "border-slate-300",
                      ].join(" ")}
                    >
                      {invoiceType === "digital" && <div className="h-2 w-2 rounded-full bg-white" />}
                    </div>
                    <div>
                      <div className="font-semibold text-sm">Digital</div>
                      <div
                        className={`text-xs ${invoiceType === "digital" ? "text-white/70" : "text-slate-500"}`}
                      >
                        Sent to your email as PDF
                      </div>
                    </div>
                  </label>

                  <label
                    className={[
                      "flex items-center gap-3 rounded-xl border-2 px-4 py-3 cursor-pointer transition",
                      invoiceType === "physical"
                        ? "border-slate-900 bg-slate-900 text-white"
                        : "border-slate-200 bg-white text-slate-900 hover:border-slate-300",
                    ].join(" ")}
                  >
                    <input
                      type="radio"
                      name="invoiceType"
                      value="physical"
                      checked={invoiceType === "physical"}
                      onChange={(e) => setInvoiceType(e.target.value as InvoiceType)}
                      className="sr-only"
                    />
                    <div
                      className={[
                        "h-5 w-5 rounded-full border-2 flex items-center justify-center",
                        invoiceType === "physical" ? "border-white" : "border-slate-300",
                      ].join(" ")}
                    >
                      {invoiceType === "physical" && <div className="h-2 w-2 rounded-full bg-white" />}
                    </div>
                    <div>
                      <div className="font-semibold text-sm">Physical</div>
                      <div
                        className={`text-xs ${invoiceType === "physical" ? "text-white/70" : "text-slate-500"}`}
                      >
                        Printed & delivered with your order
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Footer */}
        <div className="border-t border-slate-200 bg-white px-5 sm:px-8 py-4 flex items-center justify-between">
          <button
            type="button"
            onClick={() => setOpen(false)}
            className="text-slate-700 font-medium hover:text-slate-900 transition"
          >
            Cancel
          </button>
          <button
            type="button"
            onClick={handleCheckout}
            className="rounded-xl bg-slate-900 text-white font-semibold px-6 py-3 hover:bg-slate-800 active:scale-[0.98] transition shadow-lg shadow-slate-900/20"
          >
            Request for ₱{totalPrice.toFixed(2)}
          </button>
        </div>
      </div>

      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-4px); }
          to { opacity: 1; transform: translateY(0); }
        }
      `}</style>
    </div>
  );
}
