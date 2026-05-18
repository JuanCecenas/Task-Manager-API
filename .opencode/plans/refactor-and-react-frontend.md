# Backend Refactoring + React Frontend Implementation Plan

## Overview
Refactor the existing PHP Task Manager API to fix bugs and improve architecture, then build a minimalist React frontend with TypeScript and Vite.

---

## Phase 1: Backend Refactoring

### 1.1 Fix `src/Services/TaskService.php`

**Current Issues:**
- Creates artificial task ID using `$this->tasks` array instead of using DB-generated ID
- `update()` returns input data instead of the actual updated record from DB
- No validation for empty/whitespace-only titles
- No check if task exists before update/delete operations
- Constructor directly instantiates `TaskRepository` (tight coupling)

**Changes:**
- Remove `$this->tasks` array entirely
- Use constructor dependency injection with `TaskRepositoryInterface`
- In `create()`: Let repository handle ID generation, return the created task from DB
- In `update()`: Check if task exists first, validate title if provided, return updated record from DB
- In `delete()`: Check if task exists before deleting, throw 404 if not found
- Add `getById()` method to fetch single task
- Use HTTP-appropriate exception codes (404 for not found, 422 for validation errors)

### 1.2 Fix `src/Repositories/TaskRepository.php`

**Current Issues:**
- `update()` doesn't fetch the updated record after UPDATE query
- No `getById()` method for single task retrieval
- No error handling for database failures

**Changes:**
- Add `getById(int $id): ?array` method
- Modify `update()` to fetch and return the updated record after the UPDATE query
- Add proper error handling with exceptions
- All methods should return consistent data structures

### 1.3 Update `src/Repositories/Contracts/TaskRepositoryInterface.php`

**Changes:**
- Add `getById(int $id): ?array` method signature

### 1.4 Refactor `src/Controllers/TaskController.php`

**Changes:**
- Add dependency injection for `TaskService` via constructor
- Add `show(Request $request, Response $response): void` method for `GET /tasks/{id}`
- Wrap all methods in try/catch blocks
- Return proper HTTP status codes:
  - 200 for successful GET/UPDATE/DELETE
  - 201 for successful CREATE
  - 404 for task not found
  - 422 for validation errors
  - 500 for unexpected server errors
- Format error responses consistently: `{"error": "message"}`

### 1.5 Enhance `src/Core/Response.php`

**Changes:**
- Add `error(string $message, int $status): void` helper method
- Add support for CORS headers in responses

### 1.6 Update `Router.php` for CORS

**Changes:**
- Add OPTIONS method support for CORS preflight requests
- Add CORS headers to all responses:
  - `Access-Control-Allow-Origin: *`
  - `Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS`
  - `Access-Control-Allow-Headers: Content-Type, Authorization`
  - `Access-Control-Max-Age: 86400`

### 1.7 Update `public/index.php`

**Changes:**
- Add `GET /tasks/{id}` route pointing to `TaskController::show`
- Update root `/` endpoint to include the new endpoint in documentation
- Add CORS preflight handling

### 1.8 Database Migration

**Changes:**
- Create new Phinx migration to add indexes for better performance
- Ensure schema is production-ready

---

## Phase 2: React Frontend

### 2.1 Project Structure

Create `frontend/` directory with Vite + React + TypeScript:

```
frontend/
├── index.html
├── package.json
├── vite.config.ts
├── tsconfig.json
├── tsconfig.node.json
├── src/
│   ├── main.tsx
│   ├── App.tsx
│   ├── api/
│   │   └── client.ts              # Fetch API wrapper
│   ├── components/
│   │   ├── TaskApp.tsx            # Main app container
│   │   ├── TaskHeader.tsx         # App header with title
│   │   ├── TaskForm.tsx           # Create/edit task form
│   │   ├── TaskList.tsx           # List of tasks
│   │   ├── TaskItem.tsx           # Single task item
│   │   └── EmptyState.tsx         # Empty state component
│   ├── hooks/
│   │   └── useTasks.ts            # Custom hook for task operations
│   ├── types/
│   │   └── index.ts               # TypeScript interfaces
│   └── styles/
│       └── global.css             # Global styles + variables
└── public/
    └── vite.svg
```

### 2.2 Design System

**Color Palette:**
- Background: `#fafafa` (light gray)
- Card: `#ffffff` (white)
- Primary: `#6366f1` (indigo-500)
- Primary hover: `#4f46e5` (indigo-600)
- Text primary: `#111827` (gray-900)
- Text secondary: `#6b7280` (gray-500)
- Border: `#e5e7eb` (gray-200)
- Success: `#10b981` (emerald-500)
- Danger: `#ef4444` (red-500)
- Pending badge: `#f59e0b` (amber-500)

**Typography:**
- Font family: System UI stack (-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto)
- Sizes: 14px base, 16px for headings, 12px for badges

**Spacing:**
- Scale: 4px, 8px, 12px, 16px, 24px, 32px

**Shadows:**
- Card shadow: `0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06)`
- Hover shadow: `0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05)`

### 2.3 Component Specifications

**TaskApp.tsx**
- Main container component
- Manages task state (loading, error, tasks)
- Provides CRUD operations to child components

**TaskHeader.tsx**
- Minimalist header with app title
- Subtitle with task count

**TaskForm.tsx**
- Inline form for creating new tasks
- Input field with submit button
- Validation: required, max 255 chars
- Can switch to edit mode for existing tasks

**TaskList.tsx**
- Renders list of TaskItem components
- Handles loading state (skeleton loader)
- Handles empty state

**TaskItem.tsx**
- Displays task title and status badge
- Inline editing capability
- Toggle status button (pending/completed)
- Delete button with confirmation
- Hover effects for action buttons

**EmptyState.tsx**
- Friendly message when no tasks exist
- Call to action to create first task

### 2.4 API Client (`src/api/client.ts`)

```typescript
const API_BASE_URL = 'http://localhost:8000';

interface Task {
  id: number;
  title: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export const tasksApi = {
  getAll: () => fetch(`${API_BASE_URL}/tasks`).then(res => res.json()),
  getById: (id: number) => fetch(`${API_BASE_URL}/tasks/${id}`).then(res => res.json()),
  create: (data: { title: string }) => fetch(`${API_BASE_URL}/tasks`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }).then(res => res.json()),
  update: (id: number, data: { title?: string; status?: string }) => fetch(`${API_BASE_URL}/tasks/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }).then(res => res.json()),
  delete: (id: number) => fetch(`${API_BASE_URL}/tasks/${id}`, {
    method: 'DELETE',
  }).then(res => res.json()),
};
```

### 2.5 Custom Hook (`src/hooks/useTasks.ts`)

```typescript
// Manages state for tasks, loading, errors
// Provides: tasks, isLoading, error, createTask, updateTask, deleteTask, toggleTaskStatus
```

### 2.6 Package.json Dependencies

```json
{
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@types/react": "^18.2.0",
    "@types/react-dom": "^18.2.0",
    "@vitejs/plugin-react": "^4.2.0",
    "typescript": "^5.3.0",
    "vite": "^5.0.0"
  }
}
```

---

## Phase 3: Integration

### 3.1 Serve Frontend

Update `public/index.php` to serve the React app in production mode, or configure Vite dev server to proxy API requests.

### 3.2 Development Setup

- Backend: `php -S localhost:8000 -t public`
- Frontend: `cd frontend && npm run dev`
- CORS configured to allow frontend-to-backend communication

---

## File Change Summary

### Files to Modify:
1. `src/Services/TaskService.php` - Complete refactor
2. `src/Repositories/TaskRepository.php` - Add getById, fix update
3. `src/Repositories/Contracts/TaskRepositoryInterface.php` - Add getById signature
4. `src/Controllers/TaskController.php` - Add show(), error handling, DI
5. `src/Core/Response.php` - Add error helpers
6. `src/Core/Router.php` - Add CORS support
7. `public/index.php` - Add routes, CORS preflight

### Files to Create (Frontend):
- `frontend/index.html`
- `frontend/package.json`
- `frontend/vite.config.ts`
- `frontend/tsconfig.json`
- `frontend/tsconfig.node.json`
- `frontend/src/main.tsx`
- `frontend/src/App.tsx`
- `frontend/src/api/client.ts`
- `frontend/src/components/TaskApp.tsx`
- `frontend/src/components/TaskHeader.tsx`
- `frontend/src/components/TaskForm.tsx`
- `frontend/src/components/TaskList.tsx`
- `frontend/src/components/TaskItem.tsx`
- `frontend/src/components/EmptyState.tsx`
- `frontend/src/hooks/useTasks.ts`
- `frontend/src/types/index.ts`
- `frontend/src/styles/global.css`

---

## Testing Strategy

### Backend Testing:
- Test all endpoints with curl/Postman
- Verify CORS headers are present
- Test error cases (missing task, empty title, etc.)

### Frontend Testing:
- Verify all CRUD operations work
- Test loading and error states
- Verify responsive design
- Test inline editing flow

---

## Risks and Mitigations

1. **CORS Issues**: Backend and frontend on different ports - mitigated by CORS headers
2. **Database Connection**: Ensure .env is configured before testing
3. **Type Safety**: TypeScript interfaces must match API responses exactly
