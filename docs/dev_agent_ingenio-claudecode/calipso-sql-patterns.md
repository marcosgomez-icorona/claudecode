# Skill: calipso-sql-patterns

**Descripción:** Patrones obligatorios para la construcción de consultas dirigidas al ERP Calipso o cualquier instancia de SQL Server.

1. **Compatibilidad:** Todas las consultas deben ser compatibles con **SQL Server 2008 R2** [11, 17]. Evita funciones modernas (como `STRING_AGG`, `FORMAT`, o paginación con `OFFSET/FETCH` que no estén soportadas en 2008).
2. **Seguridad contra Inyección:** Usa SIEMPRE consultas parametrizadas o *prepared statements* (ej. PDO en PHP). Existe **CERO TOLERANCIA** para la concatenación de strings con variables de usuario [13, 17].
3. **Restricción de Operaciones:** El diseño predeterminado es de **Solo Lectura** [18, 19].
4. **Validación:** Si se requiere algo más allá de un `SELECT` trivial, el desarrollador debe invocar obligatoriamente a un revisor SQL (`sql-server-calipso-reviewer`) [17].