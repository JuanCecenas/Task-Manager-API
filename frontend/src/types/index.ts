export interface Task {
  id: number;
  title: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface CreateTaskInput {
  title: string;
}

export interface UpdateTaskInput {
  title?: string;
  status?: string;
}
