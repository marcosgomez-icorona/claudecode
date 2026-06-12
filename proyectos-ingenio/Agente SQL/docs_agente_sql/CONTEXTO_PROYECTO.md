# Contexto del Proyecto: Agente SQL para ERP Calipso Corporate 2022

## Objetivo
Desarrollar un agente SQL capaz de automatizar la registración de facturas de compras y ventas, así como la generación de informes periódicos en la base de datos MS SQL llamada "Corona".

## Entorno Técnico
- **ERP:** Calipso Corporate 2022.
- **Base de Datos:** MS SQL Server (Base Corona).
- **Arquitectura de Datos:** Sistema de tablas espejo (Tablas estándar + Tablas extendidas prefijadas con `UD_EZI_`).

## Flujo de Trabajo Actual
1. Análisis de esquema de tablas completado.
2. Extracción de datos desde PDFs de prueba validada.
3. Diseño de script maestro de registración con transacciones y búsquedas automáticas de IDs.
