import type { InputHTMLAttributes } from 'react';

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  hint?: string;
}

export function Input({ label, hint, className = '', id, ...props }: InputProps) {
  return (
    <div className="flex flex-col gap-1">
      {label && (
        <label htmlFor={id} className="font-semibold text-text-secondary">
          {label}
        </label>
      )}
      <input
        id={id}
        className={`rounded-sm border border-border-strong bg-white px-3 py-2 text-[13px] text-text focus:border-accent focus:outline focus:outline-2 focus:outline-accent/20 ${className}`}
        {...props}
      />
      {hint && <span className="text-[11px] text-text-muted">{hint}</span>}
    </div>
  );
}
