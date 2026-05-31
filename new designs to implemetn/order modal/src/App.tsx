import { useState } from "react";
import RequestModal from "./components/RequestModal";

export default function App() {
  const [open, setOpen] = useState(true);

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-100 via-white to-zinc-100 p-8">
      <div className="mx-auto max-w-3xl space-y-6">
        <h1 className="text-3xl font-semibold tracking-tight text-slate-900">
          Print Services
        </h1>
        <p className="text-slate-500">
          Click below to open the signage request modal.
        </p>
        <button
          onClick={() => setOpen(true)}
          className="rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white shadow-sm hover:bg-slate-800"
        >
          Request Signage
        </button>
      </div>

      {open && <RequestModal onClose={() => setOpen(false)} />}
    </div>
  );
}
