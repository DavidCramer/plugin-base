/**
 * External dependencies
 */
import { createRoot } from 'react-dom/client';
import { ArchetypeProvider } from 'archetype';

/**
 * Internal dependencies
 */
import { App } from './App';
import 'archetype/styles.css';
import './styles/global.css';

const container = document.getElementById('plugin-base-root');

if (container) {
	createRoot(container).render(
		<ArchetypeProvider>
			<App />
		</ArchetypeProvider>
	);
}
