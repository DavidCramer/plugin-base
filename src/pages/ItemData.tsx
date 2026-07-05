import { ArrowLeft } from 'lucide-react';
import { useApp } from '@/context/AppContext';
import { Button } from '@/components/ui/Button';

export function ItemData() {
  const {
    currentItem,
    clearSelection,
  } = useApp();

  if (!currentItem) return null;

  return (
    <div className="flex h-full flex-col">
      <div className="flex items-center gap-2 px-3 py-3">
        <Button variant="icon" type="button" onClick={clearSelection}>
          <ArrowLeft size={15} />
        </Button>
        <span className="truncate text-[13px] font-semibold text-text" title={currentItem.slug}>
          {currentItem.slug}
        </span>
      </div>

      <div className="flex items-center justify-between px-5 pb-2">
        <span className="text-[11px] font-bold uppercase tracking-wide text-text-secondary">Data</span>
      </div>

      <div className="flex-1 overflow-y-auto px-5">
        {!currentItem.data && (
          <p className="text-text-muted">No data yet.</p>
        )}
        {currentItem.data && (
          <pre>{JSON.stringify(currentItem.data, null, 2)}</pre>
        )}
      </div>
    </div>
  );
}
