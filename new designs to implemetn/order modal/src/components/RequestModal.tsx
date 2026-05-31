import { useState } from "react";
import { X, Minus, Plus, CheckCircle2, Check } from "lucide-react";
import { cn } from "../utils/cn";

type Priority = {
  id: "standard" | "urgent" | "rush";
  name: string;
  sub: string;
  badge: string;
  multiplier: number;
  days: number;
};

const PRIORITIES: Priority[] = [
  { id: "standard", name: "Standard", sub: "2-3 days", badge: "Base", multiplier: 1, days: 3 },
  { id: "urgent", name: "Urgent", sub: "24-48h", badge: "+30%", multiplier: 1.3, days: 2 },
  { id: "rush", name: "Rush", sub: "Same day", badge: "+60%", multiplier: 1.6, days: 0 },
];

const BASE_PRICE = 500;

function formatPHP(n: number) {
  return `₱${n.toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function toInputDate(d: Date) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

export default function RequestModal({ onClose }: { onClose: () => void }) {
  const [priority, setPriority] = useState<Priority["id"]>("standard");
  const [qty, setQty] = useState(1);
  const [needDate, setNeedDate] = useState(toInputDate(new Date()));
  const [notes, setNotes] = useState("");
  const [wantInvoice, setWantInvoice] = useState<boolean>(false);

  const current = PRIORITIES.find((p) => p.id === priority)!;
  const unitPrice = BASE_PRICE * current.multiplier;
  const total = unitPrice * qty;

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      onClick={onClose}
    >
      <div
        className="relative flex max-h-[90vh] w-full max-w-xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header image */}
        <div
          className="relative h-44 w-full bg-cover bg-center"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1200&q=80')",
          }}
        >
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
          <button
            onClick={onClose}
            className="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-black/50 text-white backdrop-blur transition hover:bg-black/70"
            aria-label="Close"
          >
            <X className="h-4 w-4" />
          </button>
          <div className="absolute bottom-4 left-6 right-6 text-white">
            <h2 className="text-2xl font-semibold">Signage</h2>
            <p className="text-sm text-white/90">
              Professional signage printing for businesses and events.
            </p>
          </div>
        </div>

        {/* Body */}
        <div className="flex-1 overflow-y-auto px-6 py-6">
          {/* Priority */}
          <div className="mb-6">
            <div className="mb-3">
              <h3 className="text-base font-semibold text-slate-900">Priority Level</h3>
            </div>
            <div className="grid grid-cols-3 gap-3">
              {PRIORITIES.map((p) => {
                const selected = p.id === priority;
                return (
                  <button
                    key={p.id}
                    onClick={() => setPriority(p.id)}
                    className={cn(
                      "relative rounded-xl border p-3 text-left transition",
                      selected
                        ? "border-slate-900 bg-slate-900 text-white shadow-md"
                        : "border-slate-200 bg-white text-slate-800 hover:border-slate-300"
                    )}
                  >
                    <div className="text-sm font-semibold">{p.name}</div>
                    <div
                      className={cn(
                        "text-xs",
                        selected ? "text-white/70" : "text-slate-500"
                      )}
                    >
                      {p.sub}
                    </div>
                    <div className="mt-3 text-sm font-semibold">{p.badge}</div>
                    {selected && (
                      <CheckCircle2 className="absolute right-2 top-2 h-4 w-4 text-white" />
                    )}
                  </button>
                );
              })}
            </div>
          </div>

          {/* Quantity + Price */}
          <div className="mb-6 grid grid-cols-2 gap-4">
            {/* Price column */}
            <div>
              <h3 className="mb-2 text-base font-semibold text-slate-900">Price</h3>
              <div className="flex h-14 w-full items-center justify-center rounded-xl bg-slate-100 px-5 text-base font-semibold text-slate-900">
                {formatPHP(unitPrice)}
              </div>
            </div>

            {/* Quantity column */}
            <div>
              <h3 className="mb-2 text-base font-semibold text-slate-900">Quantity</h3>
              <div className="flex h-14 w-full items-center justify-between rounded-xl bg-slate-100 px-2">
                <button
                  onClick={() => setQty((q) => Math.max(1, q - 1))}
                  className="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm transition hover:bg-slate-50"
                  aria-label="Decrease"
                >
                  <Minus className="h-4 w-4" />
                </button>
                <span className="text-lg font-semibold text-slate-900">{qty}</span>
                <button
                  onClick={() => setQty((q) => q + 1)}
                  className="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm transition hover:bg-slate-50"
                  aria-label="Increase"
                >
                  <Plus className="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>

          {/* Date */}
          <div className="mb-6">
            <h3 className="mb-2 text-base font-semibold text-slate-900">
              When do you need this?
            </h3>
            <div className="relative">
              <input
                type="date"
                value={needDate}
                onChange={(e) => setNeedDate(e.target.value)}
                className="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-800 focus:border-slate-400 focus:outline-none"
              />
            </div>
          </div>

          {/* Notes */}
          <div className="mb-6">
            <h3 className="mb-2 text-base font-semibold text-slate-900">Additional notes</h3>
            <textarea
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              rows={3}
              placeholder="Quantity, specifications, file references..."
              className="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none"
            />
          </div>

          {/* Invoice */}
          <div className="mb-2">
            <button
              type="button"
              onClick={() => setWantInvoice((v) => !v)}
              className={cn(
                "flex w-full items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left text-sm font-medium transition",
                wantInvoice
                  ? "border-slate-900 text-slate-900"
                  : "border-slate-200 text-slate-700 hover:border-slate-300"
              )}
            >
              <span
                className={cn(
                  "flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-md border transition",
                  wantInvoice
                    ? "border-slate-900 bg-slate-900 text-white"
                    : "border-slate-300 bg-white"
                )}
              >
                {wantInvoice && <Check className="h-3.5 w-3.5" strokeWidth={3} />}
              </span>
              I want an invoice for this order
            </button>
          </div>
        </div>

        {/* Footer */}
        <div className="flex items-center justify-between gap-3 border-t border-slate-100 bg-white px-6 py-4">
          <button
            onClick={onClose}
            className="text-sm font-medium text-slate-500 hover:text-slate-700"
          >
            Cancel
          </button>
          <button className="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
            Request for {formatPHP(total)}
          </button>
        </div>
      </div>
    </div>
  );
}
