SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[REPORT_INVOICE_LINE]
	@FK_FINANCE_INVOICE INT
AS
BEGIN

	SET NOCOUNT ON;

	;WITH ALL_DATA AS (
		SELECT
			2 AS FLAG,
			FIL.FK_FINANCE_INVOICE_SECTION,
			FIS.[DESCRIPTION] AS [SECTION_DESCRIPTION],
			SUM(FIL.QUANTITY) AS QUANTITY,
			FIL.[DESCRIPTION],
			FV.[PERCENTAGE],
			FIL.PRICE,
			SUM(FIL.PRICE_TOTAL) AS PRICE_TOTAL,
			FIL.PRICE_INCVAT,
			SUM(FIL.PRICE_TOTAL_INCVAT) AS PRICE_TOTAL_INCVAT,
			FIL.IS_COMPENSATION
		FROM FINANCE_INVOICE_LINE FIL WITH (NOLOCK)
		LEFT JOIN FINANCE_VAT FV WITH (NOLOCK) ON FV.ID = FIL.FK_FINANCE_VAT
		LEFT JOIN FINANCE_INVOICE_SECTION FIS WITH (NOLOCK) ON FIS.ID = FIL.FK_FINANCE_INVOICE_SECTION
		WHERE FIL.FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE
		  AND FIL.ACTIVE = 1
		  AND (FIL.IS_COMPENSATION IS NULL OR FIL.IS_COMPENSATION = 0)
		GROUP BY 
		  FIL.FK_FINANCE_INVOICE_SECTION,
		  FIS.[DESCRIPTION],
		  FIL.[DESCRIPTION],
		  FV.[PERCENTAGE],
		  FIL.PRICE,
		  FIL.PRICE_INCVAT,
		  FIL.IS_COMPENSATION

		UNION ALL

		SELECT
			2 AS FLAG,
			FIL.FK_FINANCE_INVOICE_SECTION,
			FIS.[DESCRIPTION] AS [SECTION_DESCRIPTION],
			1 AS QUANTITY,
			FIL.[DESCRIPTION],
			FV.[PERCENTAGE],
			SUM(FIL.PRICE_TOTAL) AS PRICE,
			SUM(FIL.PRICE_TOTAL) AS PRICE_TOTAL,
			SUM(FIL.PRICE_TOTAL_INCVAT) AS PRICE_INCVAT,
			SUM(FIL.PRICE_TOTAL_INCVAT) AS PRICE_TOTAL_INCVAT,
			FIL.IS_COMPENSATION
		FROM FINANCE_INVOICE_LINE FIL WITH (NOLOCK)
		LEFT JOIN FINANCE_VAT FV WITH (NOLOCK) ON FV.ID = FIL.FK_FINANCE_VAT
		LEFT JOIN FINANCE_INVOICE_SECTION FIS WITH (NOLOCK) ON FIS.ID = FIL.FK_FINANCE_INVOICE_SECTION
		WHERE FIL.FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE
		  AND FIL.ACTIVE = 1
		  AND FIL.IS_COMPENSATION = 1
		GROUP BY 
		  FIL.FK_FINANCE_INVOICE_SECTION,
		  FIS.[DESCRIPTION],
		  FIL.[DESCRIPTION],
		  FV.[PERCENTAGE],
		  FIL.IS_COMPENSATION
	), INCLUDE_SECTION AS (
		SELECT DISTINCT
			1 AS FLAG,
			AD.FK_FINANCE_INVOICE_SECTION,
            AD.SECTION_DESCRIPTION,
            NULL AS QUANTITY,
            AD.SECTION_DESCRIPTION AS [DESCRIPTION],
            NULL AS PERCENTAGE,
            NULL AS PRICE,
            NULL AS PRICE_TOTAL,
            NULL AS PRICE_INCVAT,
            NULL AS PRICE_TOTAL_INCVAT,
			NULL AS IS_COMPENSATION
		FROM ALL_DATA AD
		WHERE AD.FK_FINANCE_INVOICE_SECTION IS NOT NULL

		UNION ALL

		SELECT
			AD.FLAG,
			AD.FK_FINANCE_INVOICE_SECTION,
            AD.SECTION_DESCRIPTION,
            AD.QUANTITY,
            AD.DESCRIPTION,
            AD.PERCENTAGE,
            AD.PRICE,
            AD.PRICE_TOTAL,
            AD.PRICE_INCVAT,
            AD.PRICE_TOTAL_INCVAT,
			AD.IS_COMPENSATION
		FROM ALL_DATA AD
	)

	SELECT 
		* 
	FROM INCLUDE_SECTION
	ORDER BY [FK_FINANCE_INVOICE_SECTION], [FLAG], [DESCRIPTION]

END

GO
