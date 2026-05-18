import type { Task, CreateTaskInput, UpdateTaskInput } from '../types';

const API_BASE_URL = 'http://localhost:8000';

async function fetchJson<T>(url: string, options?: RequestInit): Promise<T> {
  const response = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...options?.headers,
    },
  });

  if (!response.ok) {
    const error = await response.json().catch(() => ({ error: 'Request failed' }));
    throw new Error(error.error || `HTTP ${response.status}`);
  }

  return response.json();
}

export const tasksApi = {
  getAll: () => fetchJson<Task[]>(`${API_BASE_URL}/tasks`),

  getById: (id: number) => fetchJson<Task>(`${API_BASE_URL}/tasks/${id}`),

  create: (data: CreateTaskInput) =>
    fetchJson<Task>(`${API_BASE_URL}/tasks`, {
      method: 'POST',
      body: JSON.stringify(data),
    }),

  update: (id: number, data: UpdateTaskInput) =>
    fetchJson<Task>(`${API_BASE_URL}/tasks/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    }),

  delete: (id: number) =>
    fetchJson<{ deleted: boolean }>(`${API_BASE_URL}/tasks/${id}`, {
      method: 'DELETE',
    }),
};
