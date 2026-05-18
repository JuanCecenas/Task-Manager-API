interface TaskHeaderProps {
  taskCount: number;
}

export function TaskHeader({ taskCount }: TaskHeaderProps) {
  return (
    <header className="app-header">
      <div className="header-content">
        <h1 className="app-title">Task Manager</h1>
        <p className="app-subtitle">
          {taskCount === 0 ? 'No tasks yet' : `${taskCount} task${taskCount !== 1 ? 's' : ''}`}
        </p>
      </div>
    </header>
  );
}
