SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE FUNCTION [dbo].[CRM_CONTACT_ATTN_NAME] (
	@FK_CRM_CONTACT INT,
	@FK_LANGUAGE INT
)
RETURNS NVARCHAR(1000)
AS
BEGIN

	RETURN (
		SELECT
			SALUTATION_NAME = 
				ISNULL(LTRIM(RTRIM(NULLIF(CD.TL_VALUE_TEXT, ''))) + ' ', '') +
				ISNULL(LTRIM(RTRIM(NULLIF(CC.INITIALS, ''))) + ' ', '') +
				ISNULL(LTRIM(RTRIM(NULLIF(CC.PREPOSITION, ''))) + ' ', '') + 
				ISNULL(LTRIM(RTRIM(CC.LASTNAME)), '')
		FROM CRM_CONTACT CC WITH (NOLOCK)
		OUTER APPLY (
			SELECT
				CD.TL_VALUE_TEXT
			FROM dbo.V_TL_CORE_DROPDOWNVALUE(@FK_LANGUAGE) CD 
			WHERE CD.ID = CC.FK_CORE_DROPDOWNVALUE_ATTN
		) CD
		WHERE CC.ID = @FK_CRM_CONTACT
)

END
GO
