import type { ItemDetail, ItemSummary } from '@/types';

interface PluginBaseData {
  apiBase: string;
  nonce: string;
  version: string;
}

function readData(): PluginBaseData {
  const el = document.getElementById('plugin-base-data');
  if (!el?.textContent) {
    return { apiBase: '/wp-json/plugin-base/v1', nonce: '', version: '' };
  }
  try {
    return JSON.parse(el.textContent);
  } catch {
    return { apiBase: '/wp-json/plugin-base/v1', nonce: '', version: '' };
  }
}

const data = readData();

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${data.apiBase}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': data.nonce,
      ...(options.headers ?? {}),
    },
  });

  if (!res.ok) {
    let message = `Request failed (${res.status})`;
    try {
      const body = await res.json();
      if (body?.message) {
        message = body.message;
      }
    } catch {
      // Response wasn't JSON — keep the generic message.
    }
    throw new Error(message);
  }

  if (res.status === 204) {
    return undefined as T;
  }

  return res.json();
}

export const api = {
  listItems: () => request<ItemSummary[]>('/items'),

  getItem: (slug: string) => request<ItemDetail>(`/items/${slug}`),

  createItem: (slug: string, data: object) =>
    request<ItemDetail>('/items', {
      method: 'POST',
      body: JSON.stringify({ slug, data }),
    }),

  updateItem: (slug: string, data: object) =>
    request<ItemDetail>(`/items/${slug}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    }),

  deleteItem: (slug: string) => request<void>(`/items/${slug}`, { method: 'DELETE' }),
  data,
};
