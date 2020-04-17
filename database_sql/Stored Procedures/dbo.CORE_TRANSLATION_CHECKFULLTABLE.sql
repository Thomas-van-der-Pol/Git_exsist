SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[CORE_TRANSLATION_CHECKFULLTABLE]  
	@TABLENAME NVARCHAR(150)
AS
BEGIN
	SET CONCAT_NULL_YIELDS_NULL ON;
	SET ARITHABORT ON;
	SET ANSI_PADDING ON;
	SET ANSI_WARNINGS ON;
	SET NOCOUNT ON;
	
	BEGIN TRY
		BEGIN TRANSACTION

		DECLARE @DEFAULT_LANGUAGE INT
		SELECT @DEFAULT_LANGUAGE = ID FROM CORE_LANGUAGE WHERE [DEFAULT_LANGUAGE] = 1

		DECLARE @FIELDNAME NVARCHAR(150)
		
		DECLARE @SQL NVARCHAR(MAX) 

		DECLARE @CHECKFULLTABLE_ColumnNames TABLE (ID INT, COLUMN_NAME NVARCHAR(250))
		DECLARE @I INT = 1
		DECLARE @COUNT INT

		INSERT INTO @CHECKFULLTABLE_ColumnNames
		SELECT 
			ROW_NUMBER() OVER (PARTITION BY TABLE_NAME ORDER BY INFORMATION_SCHEMA.COLUMNS.ORDINAL_POSITION),
			COLUMN_NAME			
		FROM 
			INFORMATION_SCHEMA.COLUMNS
		WHERE 
			TABLE_NAME = @TABLENAME AND
			COLUMN_NAME LIKE 'TL_%'
		ORDER BY 
			INFORMATION_SCHEMA.COLUMNS.ORDINAL_POSITION	
		
		SET @COUNT = @@rowcount
		
 		WHILE @I <= @COUNT
		BEGIN	

			SELECT 
				@FIELDNAME = COLUMN_NAME 
			FROM @CHECKFULLTABLE_ColumnNames
			WHERE ID = @I
			
			SET @SQL =
			N'
			SET NOCOUNT ON 

			INSERT INTO CORE_TRANSLATION_KEY
			(
				ID,
			    TABLENAME,
			    FIELDNAME,
			    PK
			)
			SELECT
				NEXT VALUE FOR dbo.ID_CORE_TRANSLATION_KEY,
				'''+@TABLENAME+''',
				'''+@FIELDNAME+''',
				ID
			FROM 
				'+@TABLENAME+'
			WHERE
				'+@FIELDNAME+' IS NULL
			'
						
			EXECUTE sp_executesql @SQL
				
			SET @SQL =
			N'
			SET NOCOUNT ON 

			UPDATE T SET
				T.'+@FIELDNAME+' = CORE_TRANSLATION_KEY.ID
			FROM
				'+@TABLENAME+' T
				LEFT JOIN CORE_TRANSLATION_KEY ON [TABLENAME] = '''+@TABLENAME+''' AND FIELDNAME = '''+@FIELDNAME+''' AND PK = T.ID
			WHERE
				'+@FIELDNAME+' IS NULL
			'
			
			EXECUTE sp_executesql @SQL

			SET @SQL =
			N'
			SET NOCOUNT ON 

			INSERT INTO CORE_TRANSLATION
			(
				ID,
				FK_CORE_TRANSLATION_KEY,
				FK_CORE_LANGUAGE
			)
			SELECT 
				NEXT VALUE FOR dbo.ID_CORE_TRANSLATION,
				ID,
				'+CONVERT(NVARCHAR, @DEFAULT_LANGUAGE)+'
			FROM 
				CORE_TRANSLATION_KEY
			WHERE
				NOT EXISTS
				(SELECT 1 FROM CORE_TRANSLATION WHERE FK_CORE_TRANSLATION_KEY = CORE_TRANSLATION_KEY.ID)
			'
			EXECUTE sp_executesql @SQL
					
			SET @I = @I + 1 
		END

		COMMIT TRANSACTION

	END TRY
	BEGIN CATCH
		PRINT 'Error on line ' + CAST(ERROR_LINE() AS VARCHAR(10))
		PRINT ERROR_MESSAGE()
		ROLLBACK TRANSACTION
	END CATCH
END

GO
