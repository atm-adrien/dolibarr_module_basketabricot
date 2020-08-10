ALTER TABLE llx_propal ADD CONSTRAINT fk_basketabricot_match_fk_soc1 FOREIGN KEY (fk_soc1) REFERENCES llx_societe (rowid);
ALTER TABLE llx_propal ADD CONSTRAINT fk_basketabricot_match_fk_soc2 FOREIGN KEY (fk_soc2) REFERENCES llx_societe (rowid);
