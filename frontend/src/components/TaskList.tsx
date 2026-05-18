import { TaskItem } from './TaskItem';
import type { Task } from '../types';

interface TaskListProps {
  tasks: Task[];
  editingId: number | null;
  onEdit: (id: number | null) => void;
  onUpdate: (id: number, title: string) => Promise<void>;
  onToggle: (id: number) => Promise<void>;
  onDelete: (id: number) => Promise<void>;
}

export function TaskList({ tasks, editingId, onEdit, onUpdate, onToggle, onDelete }: TaskListProps) {
  return (
    <div className="task-list">
      {tasks.map(task => (
        <TaskItem
          key={task.id}
          task={task}
          isEditing={editingId === task.id}
          onStartEdit={() => onEdit(task.id)}
          onCancelEdit={() => onEdit(null)}
          onUpdate={onUpdate}
          onToggle={onToggle}
          onDelete={onDelete}
        />
      ))}
    </div>
  );
}
