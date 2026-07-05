import { ArrowLeft } from 'lucide-react';
import { ItemList } from '@/pages/ItemList';
import { SideBar } from '@/components/ui/SideBar';
import { ItemData } from '@/pages/ItemData';
import { useApp } from '@/context/AppContext';

export function ItemViewer () {
  const { currentItem } = useApp();
  return (
    <div className={`flex flex-row h-full w-full items-start`}>
      <SideBar>
        <ItemList />
      </SideBar>
      {currentItem && (
        <ItemData />
      )}
    </div>
  );
}
