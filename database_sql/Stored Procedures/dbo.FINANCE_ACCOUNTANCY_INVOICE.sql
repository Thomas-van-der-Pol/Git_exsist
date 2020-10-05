SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[FINANCE_ACCOUNTANCY_INVOICE]
	@FILTER NVARCHAR(MAX) = '',
	@IDs NVARCHAR(MAX) = ''
AS
BEGIN

	SET NOCOUNT ON;

	SELECT
		FI.ID,
		IIF(FI.PRICE_TOTAL_INCL < 0, 21 /* CREDIT */, 20 /* INVOICE */) AS [TYPE],
		IIF(FI.PRICE_TOTAL_INCL < 0, 'Creditfactuur', 'Factuur') AS [TYPE_DESCRIPTION],
		YEAR(FI.DATE) AS [FINANCIAL_YEAR],
		MONTH(FI.DATE) AS [FINANCIAL_PERIOD],
		CR.NUMBER_DEBTOR AS DEBTORNUMBER,
		FI.NUMBER,
		ISNULL(CR.NAME + ' - ', '') + CONVERT(NVARCHAR, FI.NUMBER) AS [DESCRIPTION],
		FI.DATE,
		DATEDIFF(DAY, FI.DATE, FI.EXPIRATION_DATE) AS [PAYMENT_CONDITION],
		FI.PRICE_TOTAL_EXCL,
		FI.PRICE_TOTAL_INCL,
		CONVERT(NVARCHAR, FI.ID) AS [REFERENCE],
		PAP.QUOTATION_NUMBER,
		FI.EXACT_INVOICE_ERROR
	FROM FINANCE_INVOICE FI WITH (NOLOCK)
	JOIN CRM_RELATION CR WITH (NOLOCK) ON CR.ID = FI.FK_CRM_RELATION
	LEFT JOIN PROJECT_ASSORTMENT_PRODUCT PAP WITH (NOLOCK) ON PAP.ID = FI.FK_PROJECT_ASSORTMENT_PRODUCT
	WHERE FI.FK_CORE_WORKFLOWSTATE = [dbo].[CORE_WORKFLOWSTATE_INVOICE_FINAL]()
	  AND FI.NUMBER IS NOT NULL
	  AND CR.NUMBER_DEBTOR IS NOT NULL
	  AND (
	    FI.EXACT_INVOICE_LASTSYNC IS NULL
		OR
		FI.TS_LASTMODIFIED > FI.EXACT_INVOICE_LASTSYNC
	  ) AND (
		((@FILTER IS NULL) OR (@FILTER = ''))
		OR
		CR.NAME LIKE '%'+@FILTER+'%'
		OR
		FI.NUMBER LIKE '%'+@FILTER+'%'
		OR
		CR.NUMBER_DEBTOR LIKE '%'+@FILTER+'%'
	  ) AND (
		((@IDs IS NULL) OR (@IDs = ''))
		OR
		FI.ID IN (SELECT ID FROM dbo.CORE_INLIST(@IDs))
	  )

END

GO
