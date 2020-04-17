SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE PROCEDURE [dbo].[TRANSLATION_CATEGORY_ADVANCEDFILTER]
    @QUERYSTRING NVARCHAR(255)
AS
BEGIN
    SET CONCAT_NULL_YIELDS_NULL ON;
    SET ARITHABORT ON;
    SET ANSI_PADDING ON;
    SET ANSI_WARNINGS ON;
    SET NOCOUNT ON;

    SELECT DISTINCT
        TC.ID
    FROM TRANSLATION_CATEGORY TC WITH (NOLOCK)
    LEFT JOIN TRANSLATION_KEY TK WITH (NOLOCK) ON TK.FK_TRANSLATION_CATEGORY = TC.ID
    LEFT JOIN TRANSLATION_VALUE TV WITH (NOLOCK) ON TV.FK_TRANSLATION_KEY = TK.ID
    WHERE (
        ((@QUERYSTRING IS NULL) OR (@QUERYSTRING = ''))
        OR
        TC.CATEGORYNAME LIKE '%'+@QUERYSTRING+'%'
        OR
        TK.KEYNAME LIKE '%'+@QUERYSTRING+'%'
        OR
        TV.TEXT LIKE '%'+@QUERYSTRING+'%'
    )

END
GO
