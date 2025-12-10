import {createRoot} from 'react-dom/client';
import {App} from './App.tsx';
import '@/styles/global.css';
import {SettingsProvider} from '@/context/SettingsContext';

const root = document.getElementById('root');

if (root) {
  const config = JSON.parse(root.dataset.config || '{}');

  createRoot(root).render(
      <SettingsProvider value={config}>
        <App />
      </SettingsProvider>,
  );
}
