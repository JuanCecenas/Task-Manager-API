import { useState, FormEvent } from 'react';

interface TaskFormProps {
  onSubmit: (title: string) => Promise<void>;
  initialTitle?: string;
  onCancel?: () => void;
  isEditing?: boolean;
}

export function TaskForm({ onSubmit, initialTitle = '', onCancel, isEditing = false }: TaskFormProps) {
  const [title, setTitle] = useState(initialTitle);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    const trimmed = title.trim();
    if (!trimmed) return;

    setIsSubmitting(true);
    try {
      await onSubmit(trimmed);
      if (!isEditing) {
        setTitle('');
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className={`task-form ${isEditing ? 'task-form-edit' : 'task-form-create'}`}>
      <input
        type="text"
        value={title}
        onChange={(e) => setTitle(e.target.value)}
        placeholder={isEditing ? 'Update task title...' : 'Add a new task...'}
        className="task-input"
        maxLength={255}
        autoFocus={isEditing}
      />
      <div className="task-form-actions">
        {isEditing && onCancel && (
          <button type="button" onClick={onCancel} className="btn btn-secondary">
            Cancel
          </button>
        )}
        <button
          type="submit"
          disabled={!title.trim() || isSubmitting}
          className="btn btn-primary"
        >
          {isSubmitting ? '...' : isEditing ? 'Update' : 'Add'}
        </button>
      </div>
    </form>
  );
}
