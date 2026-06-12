/*
  Usuario readonly para MCP Calipso SQL Server
  Ejecutar en una base TEST o copia restaurada de Calipso.

  IMPORTANTE:
  - No usar credenciales reales en repositorio.
  - No ejecutar contra produccion sin autorizacion explicita.
  - Ajustar nombre de base, login, usuario y password antes de ejecutar.
  - Compatible con SQL Server 2008 R2.
*/

USE [master];
GO

CREATE LOGIN [usr_mcp_calipso_readonly]
WITH PASSWORD = 'REEMPLAZAR_PASSWORD_SEGURA',
     CHECK_POLICY = ON,
     CHECK_EXPIRATION = OFF;
GO

USE [CALIPSO_TEST];
GO

CREATE USER [usr_mcp_calipso_readonly]
FOR LOGIN [usr_mcp_calipso_readonly];
GO

EXEC sp_addrolemember N'db_datareader', N'usr_mcp_calipso_readonly';
GO

/*
  Verificacion:

  SELECT name, type_desc
  FROM sys.database_principals
  WHERE name = 'usr_mcp_calipso_readonly';

  EXECUTE AS USER = 'usr_mcp_calipso_readonly';
  SELECT TOP 1 name FROM sys.tables;
  REVERT;
*/
