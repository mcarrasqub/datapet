INSERT INTO doctor_tasks (doctor_id, title, description, status, due_date, priority, is_system, source_type, task_key, created_at, updated_at) 
VALUES 
(4, 'Revisar historia - COMPLETADA', 'Tarea completada', 'completed', '2026-04-15', 'high', 0, 'manual', 'test1', NOW(), NOW()),
(4, 'Revisar examen - COMPLETADA', 'Tarea completada', 'completed', '2026-04-18', 'medium', 0, 'manual', 'test2', NOW(), NOW()),
(4, 'Revisar radiografía - VENCIDA', 'Tarea vencida por fecha pasada', 'pending', '2026-04-10', 'high', 0, 'manual', 'test3', NOW(), NOW());
