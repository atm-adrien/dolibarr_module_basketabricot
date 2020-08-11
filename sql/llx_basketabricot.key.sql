ALTER TABLE llx_propal ADD CONSTRAINT fk_basketabricot_match_fk_soc1 FOREIGN KEY (fk_soc1) REFERENCES llx_societe (rowid);
ALTER TABLE llx_propal ADD CONSTRAINT fk_basketabricot_match_fk_soc2 FOREIGN KEY (fk_soc2) REFERENCES llx_societe (rowid);

ALTER TABLE llx_basketabricot ADD CONSTRAINT fk_basketabricot_match_terrain FOREIGN KEY (nom_terrain) REFERENCES llx_c_terrain_abricot
ALTER TABLE llx_basketabricot ADD CONSTRAINT fk_basketabricot_match_categories FOREIGN KEY (categ) REFERENCES llx_c_categories_abricot
