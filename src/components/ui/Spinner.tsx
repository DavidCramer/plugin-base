interface SpinnerProps {
  variant: 'primary' | 'secondary';
  size?: 'sm' | 'md' | 'lg';
}

export function Spinner ({ variant, size = 'md' }: SpinnerProps) {

  const sizeClasses = {
    sm: 'w-4 h-4 border-2',
    md: 'w-6 h-6 border-3',
    lg: 'w-8 h-8 border-4',
  };

  const variantClasses = {
    primary  : 'border-r-accent/50',
    secondary: 'border-r-text-secondary',
  };

  const variantClass = variantClasses[variant] || variantClasses.primary;
  const sizeClass = sizeClasses[size] || sizeClasses.md;

  return (
    <span className={`inline-block rounded-full animate-spin items-center justify-center border-border ${sizeClass} ${variantClass}`}></span>
  );
}
