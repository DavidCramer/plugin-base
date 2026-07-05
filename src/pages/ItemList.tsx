import { useEffect, useState } from 'react';
import { Plus } from 'lucide-react';
import { useApp } from '@/context/AppContext';
import { ListRow } from '@/components/ui/ListRow';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Spinner } from '@/components/ui/Spinner';

export function ItemList() {
  const { items, loadingItems, selectItem, createItem, deleteItem, currentItem, refreshItems, clearSelection } = useApp();
  const [creating, setCreating] = useState(false);
  const [value, setValue] = useState('');
  const [busy, setBusy] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);

  const submit = async () => {
    if (!value.trim()) return;
    setBusy(true);
    setFormError(null);
    try {
      await createItem(value.trim(), {});
      setValue('');
      setCreating(false);
    } catch (err) {
      setFormError(err instanceof Error ? err.message : 'Failed to create item');
    } finally {
      setBusy(false);
    }
  };


  useEffect(() => {
    refreshItems();
  }, []);

  return (
    <div className="flex h-full flex-col">
      <div className="flex items-center justify-between px-5 py-3">
        <span className="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-text-secondary">
          Items
          {loadingItems &&<Spinner variant="primary" size="sm" />}
        </span>
        <Button
          variant="icon"
          type="button"
          onClick={() => setCreating((v) => !v)}
        >
          <Plus size={16} />
        </Button>
      </div>

      {creating && (
        <div className="flex flex-col gap-2 px-3 pb-3">
          <Input
            autoFocus
            value={value}
            onChange={(e) => setValue(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && submit()}
          />
          {formError && <span className="text-[11px] text-danger">{formError}</span>}
          <div className="flex gap-2">
            <Button variant="primary" onClick={submit} disabled={busy}>
              Add
            </Button>
            <Button onClick={() => setCreating(false)}>Cancel</Button>
          </div>
        </div>
      )}

      <div className="flex-1 overflow-y-auto">
        {!loadingItems && items.length === 0 && (
          <p className="px-5 text-text-muted">No items yet.</p>
        )}
        {items.map((item, index) => (
          <ListRow
            key={`${item.slug}-${index}`}
            label={item.slug}
            onSelect={() => {
              if( item.slug === currentItem?.slug ){
                clearSelection();
                return;
              }
              selectItem(item.slug);
            }}
            onDelete={() => {
              if (confirm(`Delete item "${item.slug}"? This cannot be undone.`)) {
                deleteItem(item.slug);
              }
            }}
            showExpand={false}
            active={item.slug === currentItem?.slug}
          />
        ))}
      </div>
    </div>
  );
}
