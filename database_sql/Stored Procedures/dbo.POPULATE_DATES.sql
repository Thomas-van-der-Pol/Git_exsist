SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[POPULATE_DATES] 
	@START_DATE DATE, 
	@END_DATE DATE 
AS 
BEGIN 
	 
	SET DATEFIRST 1; 
 
	DECLARE @CURRENT_DATE DATE = @START_DATE 
	 
	DECLARE @MIN_DATE DATE = (SELECT MIN([DATE]) FROM DATES) 
	DECLARE @MAX_DATE DATE = (SELECT MAX([DATE]) FROM DATES) 
	 
	-- Datums vóór al bekende datums 
	IF (@MIN_DATE > @END_DATE) 
		SET @END_DATE = DATEADD(day, -1, @MIN_DATE) 
		 
	-- Datums ná al bekende datums 
	ELSE IF (@MAX_DATE < @START_DATE) 
		SET @CURRENT_DATE = DATEADD(day, 1, @MAX_DATE) 
 
	-- Datums half vóór al bekende datums 
	ELSE IF (@END_DATE > @MIN_DATE AND @START_DATE < @MIN_DATE AND @END_DATE < @MAX_DATE) 
		SET @END_DATE = DATEADD(DAY, -1, @MIN_DATE) 
 
	-- Datums half ná al bekende datums 
	ELSE IF (@START_DATE BETWEEN @MIN_DATE AND @MAX_DATE AND @END_DATE > @MAX_DATE) 
		SET @CURRENT_DATE = DATEADD(day, 1, @MAX_DATE) 
		 
	-- Datums tijdens al bekende datums 
	ELSE IF (@START_DATE >= @MIN_DATE AND @END_DATE <= @MAX_DATE) 
		RETURN 
		 
	PRINT @CURRENT_DATE 
	PRINT @END_DATE 
 
	WHILE @CURRENT_DATE <= @END_DATE 
	BEGIN 
		--PRINT @CURRENT_DATE 
 
		BEGIN TRY 
			INSERT INTO DATES (
				[DATE],
				[YEAR],
				[MONTH],
				[DAY],
				[DAY_NUMBER],
				[WEEK_NUMBER],
				[EVEN_WEEK],
				[START_OF_WEEK],
				[END_OF_WEEK],
				[IS_START_OF_WEEK],
				[IS_END_OF_WEEK],
				[IS_START_OF_MONTH],
				[IS_END_OF_MONTH],
				[QUARTER],
				[FRACTION]
			)
			VALUES( 
				@CURRENT_DATE, 
				DATEPART(yyyy, @CURRENT_DATE), 
				DATEPART(mm, @CURRENT_DATE), 
				DATEPART(dd, @CURRENT_DATE), 
				DATEPART(dw, @CURRENT_DATE), 
				dbo.ISO_8601_WEEK_OF_YEAR(@CURRENT_DATE), 
				CASE 
					WHEN (dbo.ISO_8601_WEEK_OF_YEAR(@CURRENT_DATE) % 2) = 0 THEN 1 
					ELSE 0 
				END, -- Even week 
				DATEADD(DAY, (DATEPART(DW, @CURRENT_DATE) - 1) * -1, @CURRENT_DATE), 
				DATEADD(DAY, 6, DATEADD(DAY, (DATEPART(DW, @CURRENT_DATE) - 1) * -1, @CURRENT_DATE)), 
				CASE	 
					WHEN @CURRENT_DATE = DATEADD(DAY, (DATEPART(dw, @CURRENT_DATE) - 1) *-1, @CURRENT_DATE) THEN 
						CAST(1 AS BIT) 
					ELSE 
						CAST(0 AS BIT) 
				END --isstartofweek, 
				, 
				CASE 
					WHEN @CURRENT_DATE = DATEADD(DAY, 6, DATEADD(DAY, (DATEPART(DW, @CURRENT_DATE) - 1) * -1, @CURRENT_DATE)) THEN 
						CAST(1 AS BIT) 
					ELSE 
						CAST(0 AS BIT) 
				END --isendofweek 
				, 
				CASE  
					WHEN DATEPART(dd, @CURRENT_DATE) = 1 THEN 
						CAST(1 AS BIT) 
					ELSE 
						CAST(0 AS BIT) 
				END--isstartofmonth 
				, 
				CASE  
					WHEN EOMONTH(@CURRENT_DATE) = @CURRENT_DATE THEN  
						CAST(1 AS BIT) 
					ELSE 
						CAST(0 AS BIT) 
				END,--isendofmont 
				DATEPART(q, @CURRENT_DATE),
				1.000000000000000000000000000000000000/DATEPART(dd,EOMONTH(@CURRENT_DATE)) -- fraction
			) 
			 
			-- Increment dag 
			SET @CURRENT_DATE = DATEADD(D, 1, @CURRENT_DATE)  
		END TRY 
		BEGIN CATCH 
			IF(@START_DATE < @MIN_DATE AND @END_DATE > @MAX_DATE) 
				SET @CURRENT_DATE = DATEADD(DAY, 1, @MAX_DATE) 
		END CATCH 
	END	 
END 
 

GO
