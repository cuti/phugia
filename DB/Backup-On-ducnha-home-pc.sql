BACKUP DATABASE [WealthyFamily]
   TO DISK = N'H:\github-gitlab\phu-gia\DB\WealthyFamily.bak'
   WITH NOFORMAT , INIT , NAME = N'WealthyFamily-Full Database Backup' , SKIP , NOREWIND , NOUNLOAD , STATS = 10 , CHECKSUM
GO

DECLARE @backupSetId AS INT
SELECT @backupSetId   = position
FROM msdb..backupset
WHERE     database_name = N'WealthyFamily'
      AND backup_set_id = (SELECT max (backup_set_id)
                             FROM msdb..backupset
                            WHERE database_name = N'WealthyFamily')

IF @backupSetId IS NULL
   RAISERROR (N'Verify failed. Backup information for database N''WealthyFamily'' not found.', 16, 1)

RESTORE VERIFYONLY
   FROM DISK = N'H:\github-gitlab\phu-gia\DB\WealthyFamily.bak'
   WITH FILE = @backupSetId , NOUNLOAD , NOREWIND
GO