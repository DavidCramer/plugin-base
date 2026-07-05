import { ChevronRight, Copy, Trash2 } from 'lucide-react';
import type { MouseEvent, ReactNode } from 'react';
import { Toggle } from './Toggle';
import { Button } from '@/components/ui/Button';

interface ListRowProps {
  label: ReactNode;
  active?: boolean;
  enabled?: boolean;
  onToggleEnabled?: (enabled: boolean) => void;
  onSelect?: () => void;
  onDuplicate?: () => void;
  onDelete?: () => void;
  showExpand?: boolean;
}

export function ListRow ({
  label,
  active = false,
  enabled,
  onToggleEnabled,
  onSelect,
  onDuplicate,
  onDelete,
  showExpand = true,
}: ListRowProps) {
  const stop = (e: MouseEvent) => e.stopPropagation();

  return (
    <div
      className={`group flex cursor-pointer items-center gap-2 pl-5 py-2 
      ${showExpand ? 'pr-2' : 'pr-5'}
      ${active ? 'bg-accent/10 text-accent' : 'text-text hover:bg-canvas'}`}
      onClick={onSelect}
    >
      {onToggleEnabled && (
        <span onClick={stop}>
          <Toggle checked={enabled ?? true} onChange={onToggleEnabled} />
        </span>
      )}

      <span className="flex-1 truncate font-medium">{label}</span>

      <div className="flex items-center gap-1">
        {showExpand && <ChevronRight />}
        {onDuplicate && (
          <Button
            type="button"
            onClick={(e) => {
              stop(e);
              onDuplicate();
            }}
            className="rounded p-1 inline-flex shrink-0 text-text-muted hover:bg-border/50 opacity-0 group-hover:opacity-100"
          >
            <Copy size={13} />
          </Button>
        )}
        {onDelete && (
          <Button
            variant={'icon'}
            type="button"
            onClick={(e) => {
              stop(e);
              onDelete();
            }}
            className="rounded p-1 text-text-muted hover:bg-red-50 hover:text-danger opacity-0 group-hover:opacity-100"
          >
            <Trash2 size={13} />
          </Button>
        )}
      </div>
    </div>
  );
}
