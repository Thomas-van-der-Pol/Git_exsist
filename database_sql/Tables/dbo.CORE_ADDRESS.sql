CREATE TABLE [dbo].[CORE_ADDRESS]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_ADDRESS_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_ADDRESS_TS_LASTMODIFIED] DEFAULT (getdate()),
[FK_CORE_COUNTRY] [int] NOT NULL,
[FK_CORE_DROPDOWNVALUE_ADRESSTYPE] [int] NULL,
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_ADDRESS_ACTIVE] DEFAULT ((1)),
[ADDRESSLINE] [nvarchar] (250) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[ZIPCODE] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[CITY] [nvarchar] (250) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[HOUSENUMBER] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_CORE_ADDRESS_TS_LASTMODIFIED]
		   ON  [dbo].[CORE_ADDRESS]
		   AFTER UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			UPDATE T SET 
				T.TS_LASTMODIFIED = GETDATE()
			FROM 
				CORE_ADDRESS  T
			INNER JOIN INSERTED I ON T.ID = I.ID
		END
GO
ALTER TABLE [dbo].[CORE_ADDRESS] ADD CONSTRAINT [PK_CORE_ADDRESS] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
