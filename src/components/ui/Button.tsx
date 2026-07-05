import type { ButtonHTMLAttributes } from 'react';

type Variant = 'primary' | 'secondary' | 'danger' | 'icon' | 'outline-primary' | 'outline-secondary';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
}

const VARIANT_CLASSES: Record<Variant, string> = {
  primary: 'bg-accent text-white hover:bg-accent-hover px-3.5 py-2',
  secondary: 'bg-white text-text border border-border-strong hover:bg-canvas px-3.5 py-2',
  danger: 'bg-white text-danger border border-danger-border hover:bg-red-50 px-3.5 py-2',
  icon: 'rounded-sm p-1 text-text-secondary hover:bg-canvas p-1',
  'outline-primary': 'bg-transparent text-accent border border-accent hover:bg-accent/10 px-3.5 py-2',
  'outline-secondary': 'bg-transparent text-text border border-border-strong hover:bg-canvas px-3.5 py-2'
};

export function Button({ variant = 'secondary', className = '', children, ...props }: ButtonProps) {
  return (
    <button
      className={`cursor-pointer inline-flex items-center gap-1.5 rounded-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-50 ${VARIANT_CLASSES[variant]} ${className}`}
      {...props}
    >
      {children}
    </button>
  );
}
