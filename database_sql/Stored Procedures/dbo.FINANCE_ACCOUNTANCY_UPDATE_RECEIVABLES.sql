SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE PROCEDURE [dbo].[FINANCE_ACCOUNTANCY_UPDATE_RECEIVABLES]
	@INVOICE_NUMBERS NVARCHAR(MAX) = ''
AS
BEGIN

	SET CONCAT_NULL_YIELDS_NULL ON;
	SET ARITHABORT ON;
	SET ANSI_PADDING ON;
	SET ANSI_WARNINGS ON;
	SET NOCOUNT ON;

	--UPDATE FINANCE_INVOICE SET
	--	PAID = 1,
	--	ALREADY_PAID = FINANCE_INVOICE.SALEPRICEINCVAT
	--WHERE FINANCE_INVOICE.EXACT_INVOICE_ID IS NOT NULL
	--  AND FINANCE_INVOICE.NUMBER NOT IN (SELECT ID FROM dbo.CORE_INLIST(@INVOICE_NUMBERS))

END
GO
