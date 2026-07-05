import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from 'react';
import { api } from '@/lib/api';
import type { ItemDetail, ItemSummary } from '@/types';

interface AppContextValue {
  version: string;
  items: ItemSummary[];
  loadingItems: boolean;
  error: string | null;

  currentItem: ItemDetail | null;
  loadingItem: boolean;
  dirty: boolean;
  saving: boolean;

  refreshItems: () => Promise<void>;
  selectItem: (id: string) => Promise<void>;
  clearSelection: () => void;
  createItem: (item: string, data: object) => Promise<void>;
  deleteItem: (id: string) => Promise<void>;

  saveCurrentItem: () => Promise<void>;
}

const AppContext = createContext<AppContextValue | null>(null);


function toMessage (err: unknown, fallback: string): string {
  return err instanceof Error ? err.message : fallback;
}

export function AppProvider ({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<ItemSummary[]>([]);
  const [loadingItems, setLoadingItems] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const [currentItem, setCurrentItem] = useState<ItemDetail | null>(null);
  const [loadingItem, setLoadingItem] = useState(false);
  const [dirty, setDirty] = useState(false);
  const [saving, setSaving] = useState(false);

  const refreshItems = useCallback(async () => {
    setLoadingItems(true);
    setError(null);
    try {
      const items = await api.listItems();
      setItems( items );
    } catch (err) {
      setError(toMessage(err, 'Failed to load items'));
    } finally {
      setLoadingItems(false);
    }
  }, []);

  const clearSelection = useCallback(() => {
    if (dirty && !confirm('You have unsaved changes. Are you sure you want to clear the selected item?')) {
      return;
    }
    setCurrentItem(null);
    setDirty(false);
  }, [dirty]);

  const selectItem = useCallback(async (slug: string) => {
    if (dirty && !confirm('You have unsaved changes. Are you sure you want to switch items?')) {
      return;
    }
    setLoadingItem(true);
    setError(null);
    try {
      setCurrentItem(await api.getItem(slug));
      setDirty(false);
    } catch (err) {
      setError(toMessage(err, 'Failed to load item'));
    } finally {
      setLoadingItem(false);
    }
  }, [dirty]);

  const createItem = useCallback(
    async (slug: string, data: object) => {
      setError(null);
      try {
        const result = await api.createItem(slug, data);
        await refreshItems();
        setCurrentItem(result);
      } catch (err) {
        setError(toMessage(err, 'Failed to create item'));
        throw err;
      }
    },
    [],
  );

  const deleteItem = useCallback(
    async (slug: string) => {
      setError(null);
      try {
        await api.deleteItem(slug);
        setCurrentItem((prev) => (prev?.slug === slug ? null : prev));
        if (currentItem?.slug === slug) {
          setDirty(false);
        }
        await refreshItems();
      } catch (err) {
        setError(toMessage(err, 'Failed to delete item'));
      }
    },
    [],
  );

  const saveCurrentItem = useCallback(async () => {
    if (!currentItem) return;
    setSaving(true);
    setError(null);
    try {
      const saved = await api.updateItem(currentItem.slug, currentItem.data);
      setCurrentItem(saved);
      setDirty(false);
      await refreshItems();
    } catch (err) {
      setError(toMessage(err, 'Failed to save item'));
      throw err;
    } finally {
      setSaving(false);
    }
  }, []);

  // Handle browser beforeunload when dirty
  useEffect(() => {
    const handleBeforeUnload = (e: BeforeUnloadEvent) => {
      if (dirty) {
        e.preventDefault();
      }
    };
    window.addEventListener('beforeunload', handleBeforeUnload);
    return () => window.removeEventListener('beforeunload', handleBeforeUnload);
  }, [dirty]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        if( dirty ) {
          saveCurrentItem();
        }
      }

    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [dirty, saveCurrentItem]);

  const value: AppContextValue = {
    version: api.data.version,
    items,
    loadingItems,
    error,
    currentItem,
    loadingItem,
    dirty,
    saving,
    refreshItems,
    selectItem,
    clearSelection,
    createItem,
    deleteItem,
    saveCurrentItem,
  };

  return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
}

export function useApp (): AppContextValue {
  const ctx = useContext(AppContext);
  if (!ctx) {
    throw new Error('useApp must be used within AppProvider');
  }
  return ctx;
}
