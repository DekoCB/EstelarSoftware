import { createRoot } from 'react-dom/client';
import GoLeftShowcase from './components/GoLeftShowcase';

const root = document.getElementById('glsRoot');
if (root) {
  createRoot(root).render(
    <GoLeftShowcase data={{ ...root.dataset }} heroMode="plain" onClose={() => { window.location.href = '/'; }} />
  );
}
