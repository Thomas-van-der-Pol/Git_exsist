SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE FUNCTION [dbo].[REMOVE_TRAILING_ZEROS] (
	@String NVARCHAR(50)
)
RETURNS NVARCHAR(50)
AS
BEGIN

	DECLARE @strValue NVARCHAR(50) 
	DECLARE @strValueR NVARCHAR(50) 
	DECLARE @strValueL NVARCHAR(50) 
	DECLARE @charInd INT 
	DECLARE @intCount INT 
		SET @strValue = @String
		SET @charInd = CHARINDEX('.',@strValue) 
	IF @charInd = 0 
		SET @strValueL = @strValue 
	ELSE 
	BEGIN 
		SET @strValueR = RIGHT(@strValue, LEN(@strValue)-@charInd) 
		SET @strValueL = LEFT(@strValue, @charInd-1) 

		SET @intCount = LEN(@strValueR)+1 
		WHILE @intCount > 0 
		BEGIN 
			SET @intCount = @intCount - 1 
			IF SUBSTRING(@strValueR, @intCount, 1) NOT LIKE '0' 
				BREAK 
		END 
	
		SELECT @strValueR = LEFT(@strValueR, @intCount) 
	END

	RETURN 
		@strValueL + 
		CASE WHEN (LEN(@strValueR) > 0) THEN 
			'.' + @strValueR 
		ELSE 
			'' 
		END

END
GO
