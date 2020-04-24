SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[FINANCE_BILLCHECK_FILL]
	 @DATE DATE,
	 @FK_CORE_DROPDOWNVALUE_ADRESSTYPE INT
AS
BEGIN
	-- Datum tabel vooruit populaten, 2 jaar terug, 2 jaar vooruit
	DECLARE @PopFrom DATE = DATEADD(MONTH,-24,GETDATE()), 
			@PopTo DATE = DATEADD(MONTH,24,GETDATE())

	EXEC dbo.POPULATE_DATES @PopFrom, @PopTo
	
	-- Alle oude data verwijderen
	DELETE FROM FINANCE_BILLCHECK

	-- PRODUCTEN
	INSERT INTO [FINANCE_BILLCHECK] (
		[FK_CORE_LABEL],
		[FK_PROJECT],
		[FK_CRM_RELATION],
		[FK_CRM_RELATION_ADDRESS],
		[FK_CRM_CONTACT],
		[FK_FINANCE_VAT],
		[FK_FINANCE_LEDGER],
		[FK_FINANCE_INVOICE_SCHEME],
		[FK_FINANCE_INVOICE_COLLECT_INTERVAL],
		[QUANTITY],
		[PRICE],
		[DESCRIPTION]
	)
	SELECT
		P.FK_CORE_LABEL,
		P.ID,
		CR.ID,
		[ADDRESS].ID,
		P.FK_CRM_CONTACT_REFERRER,
		IIF(CR.VAT_LIABLE = 1, AP.FK_FINANCE_VAT, CL.FK_FINANCE_VAT_SHIFTED),
		AP.FK_FINANCE_LEDGER,
		FIS.ID,
		ISNULL(CR.FK_FINANCE_INVOICE_COLLECT_INTERVAL, [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL_SEPARATED]()),
		PAP.QUANTITY AS [QUANTITY],
		PAP.PRICE * ISNULL((FIS.[PERCENTAGE] / 100), 1),
		AP.DESCRIPTION_EXT + IIF((FIS.[PERCENTAGE] > 0 AND FIS.[PERCENTAGE] < 100), ' (' + [dbo].[DECIMAL_TO_STRING](FIS.[PERCENTAGE]) + '%)', '')
	FROM FINANCE_INVOICE_SCHEME FIS WITH (NOLOCK)
	JOIN PROJECT_ASSORTMENT_PRODUCT PAP WITH (NOLOCK) ON PAP.ID = FIS.FK_PROJECT_ASSORTMENT_PRODUCT
	JOIN PROJECT P WITH (NOLOCK) ON P.ID = PAP.FK_PROJECT
	LEFT JOIN CORE_LABEL CL WITH (NOLOCK) ON CL.ID = P.FK_CORE_LABEL
	JOIN CRM_RELATION CR WITH (NOLOCK) ON CR.ID = P.FK_CRM_RELATION_REFERRER
	LEFT JOIN ASSORTMENT_PRODUCT AP WITH (NOLOCK) ON AP.ID = PAP.FK_ASSORTMENT_PRODUCT
	OUTER APPLY (
		SELECT TOP 1
			CRA.ID
		FROM CRM_RELATION_ADDRESS CRA
		WHERE CRA.FK_CRM_RELATION = CR.ID
		  AND CRA.ACTIVE = 1
		ORDER BY IIF(CRA.FK_CORE_DROPDOWNVALUE_ADRESSTYPE = @FK_CORE_DROPDOWNVALUE_ADRESSTYPE, 0, 1) ASC
	) [ADDRESS]
	WHERE FIS.ACTIVE = 1
	  AND PAP.ACTIVE = 1
	  AND P.ACTIVE = 1
	  AND FIS.FK_FINANCE_INVOICE_LINE IS NULL
	  AND FIS.[DATE] < @DATE


	-- COMPENSATION
	INSERT INTO [FINANCE_BILLCHECK] (
		[FK_CORE_LABEL],
		[FK_PROJECT],
		[FK_CRM_RELATION],
		[FK_CRM_RELATION_ADDRESS],
		[FK_CRM_CONTACT],
		[FK_FINANCE_VAT],
		[FK_FINANCE_LEDGER],
		[FK_FINANCE_INVOICE_COLLECT_INTERVAL],
		[QUANTITY],
		[PRICE],
		[DESCRIPTION]
	)
	SELECT
		P.FK_CORE_LABEL,
		P.ID,
		CR.ID,
		[ADDRESS].ID,
		P.FK_CRM_CONTACT_REFERRER,
		CL.FK_FINANCE_VAT_DEFAULT_COMPENSATION,
		CL.FK_FINANCE_LEDGER_DEFAULT_COMPENSATION,
		ISNULL(CR.FK_FINANCE_INVOICE_COLLECT_INTERVAL, [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL_SEPARATED]()),
		1 AS [QUANTITY],
		ROUND((ROUND(PAP.QUANTITY * ROUND((PAP.PRICE * ISNULL((FIS.[PERCENTAGE] / 100), 1)), 2), 2) * ISNULL((P.[COMPENSATION_PERCENTAGE] / 100), 1)), 2) * -1,
		'Vergoeding verzekeraar (' + [dbo].[DECIMAL_TO_STRING](P.[COMPENSATION_PERCENTAGE]) + '%)'
	FROM FINANCE_INVOICE_SCHEME FIS WITH (NOLOCK)
	JOIN PROJECT_ASSORTMENT_PRODUCT PAP WITH (NOLOCK) ON PAP.ID = FIS.FK_PROJECT_ASSORTMENT_PRODUCT
	JOIN PROJECT P WITH (NOLOCK) ON P.ID = PAP.FK_PROJECT
	LEFT JOIN CORE_LABEL CL WITH (NOLOCK) ON CL.ID = P.FK_CORE_LABEL
	JOIN CRM_RELATION CR WITH (NOLOCK) ON CR.ID = P.FK_CRM_RELATION_REFERRER
	LEFT JOIN ASSORTMENT_PRODUCT AP WITH (NOLOCK) ON AP.ID = PAP.FK_ASSORTMENT_PRODUCT
	OUTER APPLY (
		SELECT TOP 1
			CRA.ID
		FROM CRM_RELATION_ADDRESS CRA
		WHERE CRA.FK_CRM_RELATION = CR.ID
		  AND CRA.ACTIVE = 1
		ORDER BY IIF(CRA.FK_CORE_DROPDOWNVALUE_ADRESSTYPE = @FK_CORE_DROPDOWNVALUE_ADRESSTYPE, 0, 1) ASC
	) [ADDRESS]
	WHERE FIS.ACTIVE = 1
	  AND PAP.ACTIVE = 1
	  AND P.ACTIVE = 1
	  AND FIS.FK_FINANCE_INVOICE_LINE IS NULL
	  AND FIS.[DATE] < @DATE
	  AND P.COMPENSATED = 1
	  AND P.COMPENSATION_PERCENTAGE > 0


	-- MARK EVERYTHING AS UNCHECKED FOR BILLING
	UPDATE FINANCE_BILLCHECK SET 
	  DO_BILL = 0 
	WHERE DO_BILL IS NULL

END
GO
