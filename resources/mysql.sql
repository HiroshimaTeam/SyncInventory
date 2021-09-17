-- #! mysql

-- #{ SyncPLayer
-- # { init
CREATE TABLE IF NOT EXISTS SyncPLayer
(
    xuid     TEXT,
    inv      JSON,
    armor    JSON,
    ender    JSON,
    xpLvl    INT,
    xpP      FLOAT
);
-- # }
-- # { register
-- #  :xuid string
-- #  :inv string
-- #  :armor string
-- #  :ender string
-- #  :xpLvl int
-- #  :xpP float
INSERT INTO SyncPLayer (xuid, inv, armor, ender, xpLvl, xpP)
VALUES (:xuid, :inv, :armor, :ender, :xpLvl, :xpP);
-- # }
-- # {  save
-- # 	  :xuid string
SELECT *
FROM SyncPLayer
WHERE xuid = :xuid;
-- # }
-- # { update
-- #   :xuid string
-- #   :inv string
-- #   :armor string
-- #   :ender string
-- #   :xpLvl int
-- #   :xpP float
UPDATE SyncPLayer
Set inv   = :inv,
    armor = :armor,
    ender = :ender,
    xpLvl = :xpLvl,
    xpP   = :xpP
WHERE xuid = :xuid;
-- # }
-- #}

