SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE FUNCTION [dbo].[PROJECT_INVOICE_TYPE_FIXED_PRICE]()
RETURNS INT
WITH SCHEMABINDING
AS
BEGIN

	RETURN 1;

END
GO
