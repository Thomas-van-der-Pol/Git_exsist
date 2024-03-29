CREATE TABLE [dbo].[CORE_COUNTRY]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_COUNTRYS_ACTIVE] DEFAULT ((1)),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_COUNTRYS_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_COUNTRYS_TS_LASTMODIFIED] DEFAULT (getdate()),
[COUNTRYCODE] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[TL_COUNTRYNAME] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[LAT] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[LNG] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_CORE_COUNTRY_TRANSLATION]
		   ON  [dbo].[CORE_COUNTRY]
		   AFTER INSERT , UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			IF TRIGGER_NESTLEVEL() <= 1
			BEGIN

				--VOOR ELKE ROW DIE INGEVOERD IS, DE SP CORE_TRANSLATION_FILLFIELDS AANROEPEN
				DECLARE @TABLENAME NVARCHAR(150) 

				SELECT @TABLENAME = OBJECT_NAME(parent_object_id) 
							FROM sys.objects 
							WHERE sys.objects.name = OBJECT_NAME(@@PROCID)
			
				EXEC CORE_TRANSLATION_CHECKFULLTABLE @TABLENAME
		
			END
		END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_CORE_COUNTRY_TS_LASTMODIFIED]
		   ON  [dbo].[CORE_COUNTRY]
		   AFTER UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			UPDATE T SET 
				T.TS_LASTMODIFIED = GETDATE()
			FROM 
				CORE_COUNTRY  T
			INNER JOIN INSERTED I ON T.ID = I.ID
		END
GO
ALTER TABLE [dbo].[CORE_COUNTRY] ADD CONSTRAINT [PK_CORE_COUNTRYS] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_COUNTRY] ADD CONSTRAINT [FK_CORE_COUNTRY_TL_COUNTRYNAME_CORE_TRANSLATION_KEY] FOREIGN KEY ([TL_COUNTRYNAME]) REFERENCES [dbo].[CORE_TRANSLATION_KEY] ([ID])
GO
