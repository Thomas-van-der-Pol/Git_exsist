SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE FUNCTION [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL_SEPARATED]()
RETURNS INT
WITH SCHEMABINDING
AS
BEGIN

	RETURN 1;

END
GO
