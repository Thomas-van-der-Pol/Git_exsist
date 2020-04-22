SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE FUNCTION [dbo].[DECIMAL_TO_STRING] (
	@Decimal DECIMAL(15, 6)
)
RETURNS NVARCHAR(50)
AS
BEGIN

	/* Zorg dat decimals netjes worden geconverteerd naar nvarchar, geen overbodige nullen aan het einde, geen geouwehoer met 2e00-blabla */

	RETURN
		REPLACE(
			[dbo].[REMOVE_TRAILING_ZEROS](CAST(
				@Decimal
			AS NVARCHAR(1000)))
		,'.', ',')

END
GO
