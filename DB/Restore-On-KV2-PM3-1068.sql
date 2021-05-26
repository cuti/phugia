USE [master];
GO
RESTORE DATABASE [WealthyFamily] FROM DISK = N'E:\GitHub\phu-gia\DB\WealthyFamily.bak' WITH FILE = 1 , MOVE N'WealthyHouse' TO N'C:\Program Files\Microsoft SQL Server\MSSQL11.SQLEXPRESS2012\MSSQL\DATA\WealthyHouse.mdf' , MOVE N'WealthyHouse_log' TO N'C:\Program Files\Microsoft SQL Server\MSSQL11.SQLEXPRESS2012\MSSQL\DATA\WealthyHouse_log.ldf' , RECOVERY , NOUNLOAD , REPLACE , STATS = 10
GO