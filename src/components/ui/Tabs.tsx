interface TabItem {
  value: string;
  label: string;
}

interface TabsProps {
  items: TabItem[];
  value: string;
  onChange: (value: string) => void;
}

export function Tabs({ items, value, onChange }: TabsProps) {
  return (
    <div className="flex items-center gap-5 border-b border-border">
      {items.map((item) => (
        <button
          key={item.value}
          type="button"
          onClick={() => onChange(item.value)}
          className={`relative pb-2.5 text-[13px] font-semibold transition-colors cursor-pointer ${
            value === item.value ? 'text-accent' : 'text-text-secondary'
          }`}
        >
          {item.label}
          {value === item.value && <span className="absolute inset-x-0 -bottom-px h-0.5 bg-accent" />}
        </button>
      ))}
    </div>
  );
}
