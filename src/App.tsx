import { useState } from 'react';
import { Save } from 'lucide-react';
import { AppProvider, useApp } from './context/AppContext';
import { Button } from './components/ui/Button';
import { SideBar } from '@/components/ui/SideBar';
import { ListRow } from '@/components/ui/ListRow';
import { ItemViewer } from '@/views/ItemViewer';
import { ViewTemplate } from '@/views/ViewTemplate';

interface View {
  slug: string;
  title: string;
  view: React.ComponentType;
}

function Shell () {
  const { currentItem, dirty, saving, saveCurrentItem, error, version } = useApp();
  const [currentView, setCurrentView] = useState('template');

  const views: View[] = [
    {
      slug : 'template',
      title: 'View Template',
      view : ViewTemplate,
    },
    {
      slug : 'list',
      title: 'List',
      view : ItemViewer,
    },
  ];

  const CurrentView: React.ComponentType | null = views.find((v) => v.slug === currentView)?.view ?? null;

  return (
    <div className="flex h-full overflow-hidden flex-col bg-canvas text-text">
      <header className="flex h-18 shrink-0 items-center justify-between bg-header-bar px-5 text-white">
        <div className="inline-flex items-center justify-between gap-2">
          <h1 className="text-white! p-0! m-0!">PluginBase</h1>
          <span className="text-white! py-1 px-2 rounded-sm bg-white/10 font-mono text-xs">v{version}</span>
        </div>
        {currentItem && (
          <Button variant="primary" onClick={saveCurrentItem} disabled={!dirty || saving}>
            <Save size={14} />
            {saving ? 'Saving…' : 'Save changes'}
          </Button>
        )}
      </header>

      {error &&
        <div className="border-b border-danger-border bg-red-50 px-5 py-2 text-danger">{error}</div>}

      <div className="flex flex-1 overflow-hidden">

        {/* Side Bar */}
        <SideBar>
          {views.map((view: View, index) => (
            <ListRow
              key={`${view.slug}-${index}`}
              active={currentView === view.slug}
              label={view.title}
              onSelect={() => setCurrentView(view.slug)}
              showExpand={true}
            />
          ))}
        </SideBar>


        <main className="flex-1 overflow-hidden bg-canvas">
          {CurrentView && <CurrentView />}
        </main>
      </div>
    </div>
  );
}

export function App () {
  return (
    <AppProvider>
      <Shell />
    </AppProvider>
  );
}
