interface SideBarProps {
  label?: string;
  hint?: string;
  className?: string;
  children?: React.ReactNode;
}

export function SideBar({ label, hint, className = '', children}: SideBarProps) {
  return (
    <aside className={`w-72 flex flex-col h-full shrink-0 overflow-hidden border-r border-border bg-white ${className}`}>
      <div className="flex-1 overflow-y-auto">
        {children}
      </div>
    </aside>
  );
}
