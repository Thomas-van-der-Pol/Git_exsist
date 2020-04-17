CREATE TABLE [dbo].[DOCUMENT_COLLECTION]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_DOCUMENT_COLLECTION_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_DOCUMENT_COLLECTION_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_DOCUMENT_COLLECTION_ACTIVE] DEFAULT ((1)),
[FK_PROJECT] [int] NULL,
[GUID] [uniqueidentifier] NULL CONSTRAINT [DF_DOCUMENT_COLLECTION_GUID] DEFAULT (newid())
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

				CREATE TRIGGER [dbo].[TR_DOCUMENT_COLLECTION_TS_LASTMODIFIED]
						   ON  [dbo].[DOCUMENT_COLLECTION]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								DOCUMENT_COLLECTION T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[DOCUMENT_COLLECTION] ADD CONSTRAINT [PK_DOCUMENT_COLLECTION] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[DOCUMENT_COLLECTION] ADD CONSTRAINT [FK_DOCUMENT_COLLECTION_PROJECT] FOREIGN KEY ([FK_PROJECT]) REFERENCES [dbo].[PROJECT] ([ID])
GO
