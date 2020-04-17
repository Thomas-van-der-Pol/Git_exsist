SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE FUNCTION [dbo].[CORE_FORMATDATE](@date AS DATETIME, @format_string AS VARCHAR(50)  )
RETURNS VARCHAR(50) 
AS  
BEGIN 
/*
 * yy	--> Year, two digits
 * YYYY	--> Year, four digits
 * MM	--> Month, two digits
 * m	--> Month, one digit
 * DD	--> Day, two digits
 * d	--> Day, one digit
 * HH	--> Hour, two digits
 * h	--> Hour, one digit
 * NN	--> Minute, two digits
 * n	--> Minute, one digit
 * SS	--> Second, two digits
 * s	--> Second, one digit
 * AP	--> AM/PM
 * 
 * Any character not in the token list gets concatenated
 * to the string and left untouched.
 *
 * EXAMPLE: 
 * SELECT dbo.formatDate(GETDATE(), 'YYYY-MM-DD hh:nn:ss')
 * OUTPUT: 2007-01-25 17:35:21
 *
 * SELECT dbo.formatDate(GETDATE(), 'DD-MM-YYYY')
 * OUTPUT: 25-01-2007
 */
    DECLARE @format VARCHAR(50)
    DECLARE @result AS VARCHAR(50)
    DECLARE @iter AS INT
    DECLARE @prevchar AS CHAR(1) 
    DECLARE @currchar AS CHAR(1) 
    DECLARE @currtoken AS VARCHAR(4)
    

    SET @iter = 1
    SET @result = ''
    SET @format = CONVERT(VARCHAR(50),@format_string) COLLATE Latin1_General_CS_AS

    WHILE @iter <= LEN(@format)
    BEGIN
        SET @currchar = CONVERT(CHAR(1),SUBSTRING(@format,@iter,1)) COLLATE Latin1_General_CS_AS
        IF @currchar <> @prevchar OR @iter = LEN(@format)
        BEGIN
            SET @currtoken = 
                CASE (@prevchar) COLLATE Latin1_General_CS_AS -- Use a case-sensitive collation
                    WHEN 'Y' THEN RIGHT('0000' + CAST(YEAR(@date) AS VARCHAR(4)),4)
                    WHEN 'y' THEN RIGHT('00' + CAST(YEAR(@date) AS VARCHAR(4)),2)
                    WHEN 'M' THEN RIGHT('00' + CAST(MONTH(@date) AS VARCHAR(2)),2)
                    WHEN 'm' THEN CAST(MONTH(@date) AS VARCHAR(2))
                    WHEN 'D' THEN RIGHT('00' + CAST(DAY(@date) AS VARCHAR(2)),2)
                    WHEN 'd' THEN CAST(DAY(@date) AS VARCHAR(2))
                    WHEN 'H' THEN RIGHT('00' + CAST(DATEPART(HOUR,@date) AS VARCHAR(2)),2)
                    WHEN 'h' THEN CAST(DATEPART(HOUR,@date) AS VARCHAR(2))
                    WHEN 'N' THEN RIGHT('00' + CAST(DATEPART(MINUTE,@date) AS VARCHAR(2)),2)
                    WHEN 'n' THEN CAST(DATEPART(MINUTE,@date) AS VARCHAR(2))
                    WHEN 'S' THEN RIGHT('00' + CAST(DATEPART(SECOND,@date) AS VARCHAR(2)),2)
                    WHEN 's' THEN CAST(DATEPART(SECOND,@date) AS VARCHAR(2))
                    WHEN 'A' THEN CASE WHEN DATEPART(HOUR,@date) >= 12 THEN 'PM' ELSE 'AM' END
                    WHEN ' ' THEN ' '
                    ELSE RTRIM(@prevchar)
                END
            SET @result = @result + @currtoken
        END
        SET @prevchar = @currchar COLLATE Latin1_General_CS_AS
        SET @iter = @iter + 1
    END
    RETURN @result
END
GO
