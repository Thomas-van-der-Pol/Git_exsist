SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE FUNCTION [dbo].[StrList] (@list nvarchar(MAX))
   RETURNS @tbl TABLE (ID INT IDENTITY, string varchar(100) NOT NULL) AS
BEGIN
   DECLARE @pos        int,
           @nextpos    int,
           @valuelen   int

   SELECT @pos = 0, @nextpos = 1

   WHILE @nextpos > 0
   BEGIN
      SELECT @nextpos = charindex(',', @list, @pos + 1)
      SELECT @valuelen = CASE WHEN @nextpos > 0
                              THEN @nextpos
                              ELSE len(@list) + 1
                         END - @pos - 1
      INSERT @tbl (string)
         VALUES (convert(varchar(100), substring(@list, @pos + 1, @valuelen)))
      SELECT @pos = @nextpos
   END
  RETURN
END


GO
