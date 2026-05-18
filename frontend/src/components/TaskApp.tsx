import { useEffect, useState } from 'react';
import { useTasks } from '../hooks/useTasks';
import { TaskHeader } from './TaskHeader';
import { TaskForm } from './TaskForm';
import { TaskList } from './TaskList';
import { EmptyState } from './EmptyState';

export function TaskApp() {
  const { tasks, isLoading, error, fetchTasks, createTask, updateTask, deleteTask, toggleTaskStatus, setError } = useTasks();
  const [editingId, setEditingId] = useState<number | null>(null);

  useEffect(() => {
    fetchTasks();
  }, [fetchTasks]);

  const handleCreate = async (title: string) => {
    await createTask(title);
  };

  const handleUpdate = async (id: number, title: string) => {
    await updateTask(id, { title });
    setEditingId(null);
  };

  const handleToggle = async (id: number) => {
    const task = tasks.find(t => t.id === id);
    if (task) {
      await toggleTaskStatus(task);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Are you sure you want to delete this task?')) {
      await deleteTask(id);
    }
  };

  return (
    <div className="app-container">
      <TaskHeader taskCount={tasks.length} />
      
      <div className="app-content">
        <TaskForm onSubmit={handleCreate} />
        
        {error && (
          <div className="error-banner">
            <span>{error}</span>
            <button onClick={() => setError(null)} className="error-close">x</button>
          </div>
        )}
        
        {isLoading ? (
          <div className="skeleton-list">
            {[1, 2, 3].map(i => (
              <div key={i} className="skeleton-item" />
            ))}
          </div>
        ) : tasks.length === 0 ? (
          <EmptyState />
        ) : (
          <TaskList
            tasks={tasks}
            editingId={editingId}
            onEdit={setEditingId}
            onUpdate={handleUpdate}
            onToggle={handleToggle}
            onDelete={handleDelete}
          />
        )}
      </div>
    </div>
  );
}
