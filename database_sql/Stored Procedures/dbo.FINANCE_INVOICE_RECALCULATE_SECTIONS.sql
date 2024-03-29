SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[FINANCE_INVOICE_RECALCULATE_SECTIONS]
	@FK_FINANCE_INVOICE INT
AS
BEGIN

	-- Delete old invoice sections
	UPDATE FINANCE_INVOICE_LINE SET
	  FK_FINANCE_INVOICE_SECTION = NULL
	WHERE FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE

	DELETE FROM FINANCE_INVOICE_SECTION WHERE FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE

	-- Create invoice sections
	INSERT INTO FINANCE_INVOICE_SECTION (
		[FK_FINANCE_INVOICE],
		[FK_PROJECT],
		[DESCRIPTION]
	)
	SELECT DISTINCT
		FIL.FK_FINANCE_INVOICE,
		FIL.FK_PROJECT,
		P.[DESCRIPTION] + ISNULL(' - ' + P.POLICY_NUMBER, '')
	FROM FINANCE_INVOICE_LINE FIL WITH (NOLOCK)
	JOIN PROJECT P WITH (NOLOCK) ON P.ID = FIL.FK_PROJECT
	WHERE FIL.FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE
	  AND FIL.ACTIVE = 1

	-- Link invoice sections on invoice lines
	UPDATE FIL SET
	  FK_FINANCE_INVOICE_SECTION = FIS.ID
	FROM FINANCE_INVOICE_LINE FIL
	JOIN FINANCE_INVOICE_SECTION FIS WITH (NOLOCK) ON FIS.FK_FINANCE_INVOICE = FIL.FK_FINANCE_INVOICE AND FIS.FK_PROJECT = FIL.FK_PROJECT
	WHERE FIL.FK_FINANCE_INVOICE = @FK_FINANCE_INVOICE

END

GO
