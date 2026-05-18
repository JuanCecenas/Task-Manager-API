import { useState } from 'react';
import { TaskForm } from './TaskForm';
import type { Task } from '../types';

interface TaskItemProps {
  task: Task;
  isEditing: boolean;
  onStartEdit: () => void;
  onCancelEdit: () => void;
  onUpdate: (id: number, title: string) => Promise<void>;
  onToggle: (id: number) => Promise<void>;
  onDelete: (id: number) => Promise<void>;
}

export function TaskItem({ task, isEditing, onStartEdit, onCancelEdit, onUpdate, onToggle, onDelete }: TaskItemProps) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);
    try {
      await onDelete(task.id);
    } finally {
      setIsDeleting(false);
    }
  };

  if (isEditing) {
    return (
      <div className="task-item task-item-editing">
        <TaskForm
          initialTitle={task.title}
          onSubmit={(title) => onUpdate(task.id, title)}
          onCancel={onCancelEdit}
          isEditing
        />
      </div>
    );
  }

  return (
    <div className={`task-item ${task.status === 'completed' ? 'task-item-completed' : ''}`}>
      <div className="task-content">
        <button
          onClick={() => onToggle(task.id)}
          className={`task-status-btn ${task.status === 'completed' ? 'task-status-completed' : 'task-status-pending'}`}
          aria-label={`Mark as ${task.status === 'completed' ? 'pending' : 'completed'}`}
        >
          {task.status === 'completed' ? (
            <svg className="check-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
            </svg>
          ) : (
            <div className="task-circle" />
          )}
        </button>
        <span className="task-title" onDoubleClick={onStartEdit}>
          {task.title}
        </span>
      </div>
      <div className="task-actions">
        <span className={`task-badge ${task.status === 'completed' ? 'badge-completed' : 'badge-pending'}`}>
          {task.status}
        </span>
        <button onClick={onStartEdit} className="btn-icon" aria-label="Edit task">
          <svg viewBox="0 0 20 20" fill="currentColor" className="icon">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
          </svg>
        </button>
        <button
          onClick={handleDelete}
          disabled={isDeleting}
          className="btn-icon btn-icon-danger"
          aria-label="Delete task"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" className="icon">
            <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
          </svg>
        </button>
      </div>
    </div>
  );
}
